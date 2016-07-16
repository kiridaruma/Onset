#!ruby -Ku
# -*- coding: utf-8 -*-

require 'configBcDice.rb'
require 'ArgsAnalizer.rb'

class IrcClient < Net::IRC::Client
  
  def initialize(*args)
    super
    
    host, port, *options = *args
    
    @bcdiceMarker = BCDiceMaker.new
    
    @loginChannelList = $defaultLoginChannelsText.split(',')
  end
  
  def newBcDice
    bcdice = @bcdiceMarker.newBcDice()
    bcdice.setIrcClient(self)
    return bcdice
  end
  
  def readExtraCard(cardFileName)
    bcdice = newBcDice()
    bcdice.readExtraCard(cardFileName)
  end
  
  def ownNick
    @opts.nick
  end
  
  def setRoom(room)
    @room = room
  end
  
  def setGameByTitle(game_type)
    bcdice = newBcDice()
    bcdice.setGameByTitle(game_type)
  end
  
  def on_connected(*args)
    printText( '  -> IRC server is connected.' )
    
    channelNames = @loginChannelList.join(',')
    channelNames = encode($ircCode, channelNames)
    
    join(channelNames);
    topic(channelNames);
    
    printText( "login to channels(#{channelNames}), so wait a moment..." )
  end
  
  
  def on_rpl_welcome(message)
    printText( '  -> login to channel successed.' )
    #post JOIN, @room.encode($ircCode).force_encoding_maybe('external')
    post(JOIN, encode($ircCode, @room))
  end
  
  
  def on_init( event )
    args = event.args;
    
    shift( args );
    debug_out("*** #{args.ispect}\n");
  end

  def on_part( event )
    channel = getChannel(event)
    
    debug_out("*** %s has left channel %s\n", event.nick, channel);
  end
  
  
  def on_join( event )
    debug('on_join event', event)
    
    channel = getChannel(event)
    nick_e = getNickEFromEvent(event)
    host_j = event.prefix.host;
    
    debug("join nick_e, host_j, channel", nick_e, host_j, channel);
    
    if ( host_j =~ /^someone\@somewhere\.else\.com$/)  # Auto-ops anyone who
      debug_out("Give  to #{nick_e}\n");
      self.mode( encode($ircCode, channel), "+o", nick_e);      # matches hostmask.
    end
  end
  
  def on_invite( event )
    channel = getChannel(event)
    
    debug_out("*** %s (%s) has invited me to channel %s\n",
              event.nick, event.userhost, channel);
    
    addChannel(channel);
    self.join( encode($ircCode, channel) );
    self.topic( encode($ircCode, channel) );
  end
  
  def on_kick( event )
    channel = getChannel(event)
    
    mynick = self.nick;
    target = (event.to)[0];

    debug_out("%s Kicked on %s by %s.\n", target, channel);
    if( mynick == target )
      deleteChannel(channel);
    end
  end
  
  def on_msg( event )
    debug('on_msg begin')
    
    nick_e = getNickEFromEvent(event)
    channel = getChannel(event)
    
    arg = getArg(event)
    tnick = "";
    if( /->/ =~ arg )
      arg, tnick, *dummy = arg.split(/->/)
    end
    
    debug("nick_e, arg, tnick", nick_e, arg, tnick)
    
    bcdice = newBcDice()
    bcdice.setMessage(arg)
    bcdice.setChannel(channel)
    bcdice.recieveMessage(nick_e, tnick)
  end
  
  def on_privmsg(event)
    debug("=============>on_privmsg begin event", event)
    on_public(event)
  end
  
  def on_public(event)
    debug('on_public begin')
    debug('on_public event', event)
    debug('on_public begin ownNick', ownNick)
    
    channel = getChannel(event)
    debug('on_public channel', channel)

    if( channel == ownNick )
      return on_msg( event )
    end
    
    arg = getArg(event)
    
    debug('on_public arg', arg)
    
    nick_e = getNickEFromEvent(event)
    debug("on_public nick_e : #{nick_e}")
    
    bcdice = newBcDice()
    bcdice.setMessage(arg)
    bcdice.setChannel(channel)
    bcdice.recievePublicMessage(nick_e)
  end
  
  def getNickEFromEvent(event)
    nick_e = event.prefix.nick.toutf8
  end
  
  def getChannel(event)
    channel = event.params[0].toutf8
  end
  
  def getArg(event)
    arg = event.params[1].toutf8
  end
  
  def setPrintFuction(func)
    @printFunction = func
  end
  
  def printText(text)
    return if( @printFunction.nil? )
    
    @printFunction.call(text)
  end
  
  #正常な「切断」処理時にもエラーが検出される。対処法が不明のため現状コメントアウト
=begin
  def on_error(*args)
    debug_out("on_error begin, args", args.inspect)
    sendMessage( args.inspect )
  end
=end
  
  def on_err_nicknameinuse(event)
    debug_out("on_err_nicknameinuse being !")
    debug_out("@opts.nick", @opts.nick)
    
    oldNick = @opts.nick
    newNick = getNewNick(oldNick)
    @opts.nick = newNick
    debug_out("newNick", newNick)
    
    printText( "  -> nick \"#{oldNick}\" is already used, so change \"#{oldNick}\" -> \"#{newNick}\"" )
    
    post(NICK, @opts.nick)
  end
  
  def getNewNick(nick)
    debug_out("getNewNick nick", nick)
    
    @nickIndex ||= 1
    @nickIndex += 1
    @log.debug("@nickIndex:#{@nickIndex}")
    
    nickIndexText = sprintf("%d", @nickIndex)
    @log.debug("nickIndexText:#{nickIndexText}")
    
    newNick = nick + nickIndexText
    diff = newNick.length - $ircNickMaxLength
    @log.debug("newNick:#{newNick}, newNick.length#{newNick.length}")
    @log.debug("diff:#{diff}")
    
    if( diff > 0 )
      nickBase = nick[0...(diff * -1)]
      @log.debug("getNewNick nickBase:#{nickBase}")
      newNick = nickBase + nickIndexText
    end
    
    @log.debug("newNick:#{newNick}")
    return newNick
  end

=begin
  def on_ping(event)
    #nick = getNickEFromEvent(event)
    #arg = getArg(event)
    
    #ctcp_reply(nick, args);
    #debug_out("*** CTCP PING request from #{nick} received\n");
  end
  
  def on_ping_reply(event)
    args = (event.args)[1];
    nick = event.nick;
    
    args = time - args; #FIXME
    debug_out("*** CTCP PING reply from $nick: $args sec.\n");
  end
  
  def on_nick(event)
    debug('on_nick event', event)
    super
  end
  
  def on_action(event)
    nick = event.nick
    args = event.args;
    
    args.each do |arg_o|
      arg_o = decode($ircCode, arg_o);
    end
    
    debug_out("* #{nick} #{args.inspect}\n");
  end
  
  def on_disconnect(event)
    
    debug_out("Disconnected from ", event.from(), " (",
              (event.args())[0], "). Attempting to reconnect...\n");
    self.connect();
  end
  
  def on_topic(event)
    args = event.args();
    chan = decode($ircCode, (event.to())[0]);
    
    args.each do |arg_o|
      arg_o = decode($ircCode, arg_o);
    end
    
    if( event.type() == 'notopic' )
      debug_out("No topic set for #{args[1]}.\n");
    elsif ( (event.type() == 'topic') and event.to())
      debug_out("Topic change for ", chan, ": $args[0]\n");
    else
      debug_out("The topic for $args[1] is \"$args[2]\".\n");
    end
  end
=end
  
  
  def isMaster
    bcdice = newBcDice()
    bcdice.isMaster
  end
  
  def quit
    debug('quitCommand')
    debug('isMaster()', isMaster())
    
    return unless( isMaster() )
    
    post(QUIT, encode($ircCode, $quitMessage))
  end
  
  def setQuitFuction(func)
    bcdice = newBcDice()
    bcdice.setQuitFuction(func)
  end
  
  def addChannel(add_ch)
    @loginChannelList << add_ch
  end
  
  def deleteChannel(del_ch)
    @loginChannelList.delete_if{|i| i == del_ch}
  end

  def sendMessageToChannels(message)
    @loginChannelList.each do |channel|
      sendMessage(channel, message);
    end
  end

  def sendMessageToOnlySender(nick_e, message)
    sendMessage(nick_e, message)
  end
  
  def sendMessage(to, message)
    return if(message.empty?)
    
    debug('sendMessage to, message', to, message)
    
    #if( message.length > $SEND_STR_MAX)         # 長すぎる出力はイタズラと見なす
    #  message = '結果が長くなりすぎました';
    #end
    
    # 長すぎる出力は"\n"を挟み、分割送信されるように。
    message = insertEnterToTooLongMessage(message)
    
    to = encode($ircCode ,to)
    
    msg_arr = message.split("\n")
    msg_arr.each do |lineMessage|
      lineMessage = encode($ircCode, lineMessage);        # noticeで送信
      
      if( $NOTICE_SW )
        debug("notice  to, lineMessage", to, lineMessage)
        notice(to, lineMessage);        # noticeで送信
      else
        debug("privmsg  to, lineMessage", to, lineMessage)
        privmsg(to, lineMessage);       # privmsgで送信
      end
      
      sleep( 1 )
    end
  end
  
  
  def insertEnterToTooLongMessage(message)
    
    if( message.length <= $SEND_STR_MAX)
      return message
    end
    
    result = ""
    index = 1
    
    message.chars do |ch|
      result << ch
      
      if( result.length > ($SEND_STR_MAX * index) )
        result << "\n"
        index += 1
      end
    end
    
    return result
  end
  
  
  def notice(to, message)
    post(NOTICE, to, encode($ircCode, message))
  end
  
  def privmsg(to, message)
    post(PRIVMSG, to, encode($ircCode, message))
  end  
end




def getInitializedIrcBot()
  ircBot = IrcClient.new($server, $port, {
               :nick => $nick, :user => $userName, :real => $ircName
             })
  debug("$server, $port, $nick, $userName, $ircName", $server, $port, $nick, $userName, $ircName)
  
  room = $defaultLoginChannelsText.split(',').first
  ircBot.setRoom(room)
  
  ircBot.setGameByTitle( $defaultGameType )
  
  unless( $extraCardFileName.empty? )
    ircBot.readExtraCard( $extraCardFileName )
  end
  
  return ircBot
end

def mainIrcBot(args = [])
  argsAnalizer = ArgsAnalizer.new(args)
  argsAnalizer.analize
  
  ircBot = getInitializedIrcBot()
  
  if( argsAnalizer.isStartIrc )
    ircBot.start
  end
  
  return ircBot
end


