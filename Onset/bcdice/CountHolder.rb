#!/bin/ruby -Ku 
# -*- coding: utf-8 -*-

require 'log'
require 'configBcDice.rb'


class CountHolder
  
  def initialize(bcdice, countInfos)
    @bcdice = bcdice
    @countInfos = countInfos
    #=> @countInfos は
    # {:channelName => {:characterName => (カウンター情報) }
    # という形式でデータを保持します。
  end
  
  def executeCommand( command, nick, channel, pointerMode )
    debug("point_counter_command begin(command, nick, channel, pointerMode)", command, nick, channel, pointerMode )
    
    @command = command
    @nick = @bcdice.getNick(nick)
    @channel = channel
    @pointerMode = pointerMode
    
    output = "1";
    isSecret = (pointerMode == :sameNick)
    
    case @command
    when /^#OPEN!/i
      output = get_point_list();
    when /^#(.*)DIED!/i
      output = delete_point_list();
      unless( output.nil? )
        output = "#{nick}: #{output} のカウンタが削除されました";
        isSecret = true # 出力は常にTalk側
      end
    when /^#RENAME!/i
      output = rename_point_counter();
      if( output != "1" )
        output = "#{nick}: #{output}";
        isSecret = false  # 出力は常にPublic側
      end
      
    else
      if( /^#/ =~ @command )
        output = executeSetCommand();
        if( output != "1" )
          output = "#{nick}: #{output}";
        end
      end
    end
    
    debug("point_counter_command END output, isSecret", output, isSecret)
    
    return output, isSecret
  end
  

#=========================================================================
#**                       汎用ポイントカウンタ
#=========================================================================

####################          カウンタ操作         ########################
  def executeSetCommand
    debug("setCountHolder nick, channel, pointerMode", @nick, @channel, @pointerMode)
    
    @characterName = @nick
    
    @tagName = nil
    @currentValue = nil
    @maxValue = nil
    @modifyText = nil
    
    debug("$point_counter", $point_counter)
    output = '1';
    
    debug("@command", @command)
    
    case @command
    when /^#([^:：]+)(:|：)(\w+?)\s*(\d+)(\/(\d+))?/
      debug(" #(識別名):(タグ)(現在値)/(最大値) で指定します。最大値がないものは省略できます。")
      # #Zako1:HP9/9　　　#orc1:HP10/10　　#商人:HP8/8
      @characterName = $1
      @tagName = $3
      @currentValue = $4.to_i
      @maxValue = $6
    when /^#([^:：]+)(:|：)(\w+?)\s*([\+\-]\d+)/
      debug(" #(識別名):(タグ)(変更量)")
      # #Zako1:HP-1
      @characterName = $1
      @tagName = $3
      @modifyText = $4
    when /^#(\w+?)\s*(\d+)\/(\d+)/
      debug(" #(タグ)(現在値)/(最大値) 現在値/最大値指定は半角のみ。")
      # #HP12/12　　　#衝動0/10
      @tagName = $1
      @currentValue = $2.to_i
      @maxValue = $3
    when /^#(\w+?)\s*([\+\-]\d+)/
      debug(" #(タグ)(変更量)")
      # #HP-1
      @tagName = $1
      @modifyText = $2
    when /^#(\w+?)\s*(\d+)/
      debug(" #(タグ)(現在値) で指定します。現在値は半角です。")
      # #DEX12　　　#浸食率0
      @tagName = $1
      @currentValue = $2.to_i
    when /^#(\w+?)\s*([\+\-]\d+)/
      debug(" #(タグ)(変更量) ")
      # #DEX-1
      @tagName = $1
      @modifyText = $2
    else
      debug("not match command", @command)
      return ''
    end
    
    unless( @maxValue.nil? )
      @maxValue = @maxValue.to_i
    end
    
    debug("characterName", @characterName)
    debug("tagName", @tagName)
    debug("@currentValue", @currentValue)
    debug("@maxValue", @maxValue)
    debug("@modifyText", @modifyText)
    
    return setCountHolderByParams
  end
  
  
=begin
    unless(/^#((\w+?[:：])?\w+?)[\s]*([\+\-\/\d]+)/ =~ command)
      return output
    end
    
    target = $1;
    point = $3;
    
    pc = "";
    if( $2 )
      pc, target = target.split(/[:：]/)
    else
      pc = nick;
    end
    
    if(point =~ /^[\+\-]/)
      debug("ポイント操作")
      bonus = parren_killer("(0#{point})").to_i
      
      2.times do |i|
        next unless( $point_counter["#{nick},#{pc},#{target},#{i}"] )
        
        pre_point = $point_counter["#{nick},#{pc},#{target},#{i}"];
        current_point = 0;
        
        if( /(\d+)[\/](\d+)/ =~ pre_point )
          debug("現在値/最大値型")
          current = $1.to_i
          max = $2.to_i
          current = (current_point + bonus)
          current_point = "#{current}/#{max}"
        else
          degug("現在値型")
          current_point = pre_point + bonus;
        end
        
        $point_counter["#{nick},#{pc},#{target},#{i}"] = current_point;
        output = "(#{target}) #{pre_point} -> #{current_point}";
        output = "#{pc.downcase}#{output}" if(pc != nick);
        break;
      end
    else
      debug("ポイント登録")
      
      unless($point_counter["#{nick},#{pc},#{target},#{pointerMode}"])
        debug("setPrintPlotChannel(channel)", setPrintPlotChannel(channel))
        setPrintPlotChannel(channel) if(getPrintPlotChannel(nick) == "1");
      end
      
      $point_counter["#{nick},#{pc},#{target},#{pointerMode}"] = point;
      anotherIndex = (pointerMode == :sameNick ? :sameChannel : :sameNick)
      another_point = "#{nick},#{pc},#{target},#{anotherIndex}"
      debug("another_point", another_point)
      
      if( $point_counter[another_point] )
        $point_counter.delete(another_point)
      end
      
      setPointCounters(nick, pc, target)
      
      $point_counter[channel] ||= []
      members = $point_counter[channel]
      unless( members.include?(nick) )
        members << nick
      end
      
      output = "(#{target}) #{point}";
      output = "#{pc.downcase}#{output}" if($pc != $nick);
    end
    
    debug("setCountHolder end $point_counter", $point_counter)
    
    return output;
  end
=end
  
  def setCountHolderByParams
    debug("@modifyText", @modifyText)
    if( @modifyText.nil? )
      return setCount
    else
      return changeCount
    end
  end
  
  
  def setCount
    @countInfos[@channel] ||= {}
    characterInfoList = getCharacterInfoList
    characterInfoList[@characterName] ||= {}
    characterInfo = characterInfoList[@characterName]
    
    characterInfo[@tagName] = {
      :currentValue => @currentValue,
      :maxValue => @maxValue,
    }
    
    debug('setCount @nick, @characterName', @nick, @characterName)
    
    output = ""
    output << "#{@characterName.downcase}" if(@nick != @characterName)
    output << "(#{@tagName}) #{@currentValue}";
    
    debug("setCount @maxValue", @maxValue)
    unless( @maxValue.nil? )
      output << "/#{@maxValue}";
    end
    
    return output
  end
  
  def changeCount
    debug("changeCount begin")
    
    modifyValue = @bcdice.parren_killer("(0#{ @modifyText })").to_i
    characterInfo = getCharacterInfo(@channel, @characterName)
    
    info = characterInfo[@tagName]
    debug("characterInfo", characterInfo)
    debug("info", info)
    return "" if( info.nil? )
    
    currentValue = info[:currentValue]
    maxValue = info[:maxValue]
    
    preText = getValueText(currentValue, maxValue)
    
    debug("currentValue", currentValue)
    debug("modifyValue", modifyValue)
    currentValue += modifyValue
    info[:currentValue] = currentValue
    
    nowText = getValueText(currentValue, maxValue)
    
    output = ""
    output << "#{@characterName.downcase}" if(@nick != @characterName)
    output << "(#{@tagName}) #{preText} -> #{nowText}";
    
    debug("changeCount end output", output)
    
    return output
  end
  
  def getValueText(currentValue, maxValue)
    text = "#{currentValue}"
    text += "/#{maxValue}" unless( maxValue.nil? )
    
    return text
  end
  
  def getCharacterInfoList(channel = nil)
    channel ||= @channel
    
    @countInfos[channel] ||= {}
    characterInfoList = @countInfos[channel]
    
    return characterInfoList
  end
  
  def getCharacterInfo(channel, characterName)
    characterName ||= @characterName
    
    characterInfoList = getCharacterInfoList(channel)
    
    characterInfoList[characterName] ||= {}
    characterInfo = characterInfoList[characterName]
    
    return characterInfo
  end
  
####################          カウンタ一覧         ########################
  def get_point_list
    debug("get_point_list(command, nick, channel, pointerMode)", @command, @nick, @channel, @pointerMode)
    
    output = "1";
    
    return output unless(/^#OPEN![\s]*(\w*)(\s|$)/ =~ @command)
    
    tag = $1;
    case @pointerMode
    when :sameNick
      debug("same nick")
      pc_out = getPointListAtSameNick(tag)
      output = pc_out unless(pc_out.empty?);
    when :sameChannel
      if( tag )
        debug("same Channel")
        pc_out = getPointListAtSameChannel(tag)
        output = pc_out unless(pc_out.empty?);
      end
    end
    
    return output;
  end
  
  
  def getPointListAtSameNick(command, nick, channel, pointerMode, tag)
    debug("getPointListAtSameNick(command, nick, channel, pointerMode, tag)", command, nick, channel, pointerMode, tag)
    debug("同一Nick, 自キャラの一覧表示(パラメータ指定不要)")
    
    pc_list = $point_counter[nick];
    pc_out = "";
    if( pc_list )
      sort_pc = {}
      pc_list.each do |pc_o|
        if( $point_counter["#{nick},#{pc_o}"] )
          tag_out = "";
          if( tag )
            check_name = "#{nick},#{pc_o}";
            if($point_counter["#{check_name},#{tag},0"])
              sort_pc[check_name] = $point_counter["#{check_name},#{tag},0"];
            end
            if($point_counter["#{check_name},#{tag},1"])
              sort_pc[check_name] = $point_counter["#{check_name},#{tag},1"];
            end
          else
            tag_arr = $point_counter["#{nick},#{pc_o}"];
            tag_arr.each do |tag_o|
              check_name = "#{nick},#{pc_o},#{tag_o}";
              if($point_counter["#{check_name},0"])
                tag_out += "$tag_o(" + $point_counter["#{check_name},0"] + ") ";
              end
              if($point_counter["#{check_name},1"])
                tag_out += "#{tag_o}[" + $point_counter["#{check_name},1"] + "] ";
              end
            end
          end
          if(tag_out)
            debug("中身があるなら")
            pc_out += ", " if(pc_out);
            pc_out += "#{pc_o.downcase}:#{tag_out}";
          end
        end
      end
      
      if(tag)
        out_pc = "";
        pc_sorted = sort_point_hash(sort_pc);
        pc_sorted.each do |pc_o|
          pc_name = pc_o.split(/,/)
          out_pc += ", " if(out_pc);
          if($pc_name[1])
            if($point_counter["#{pc_o},#{tag},0"])
              out_pc += "#{pc_name[1].upcase}(" + $point_counter["#{pc_o},#{tag},0"] + ")";
            end
            if($point_counter["#{pc_o},#{tag},1"])
              out_pc += "#{pc_name[1].upcase}[" + $point_counter["#{pc_o},#{tag},1"] + "]";
            end
          else
            if($point_counter["#{pc_o},#{tag},0"])
              out_pc += "#{pc_name[0].upcase}(" + $point_counter["#{pc_o},#{tag},0"] + ")";
            end
            if($point_counter["#{pc_o},#{tag},1"])
              out_pc += "#{pc_name[0].upcase}[" + $point_counter["#{pc_o},#{tag},1"] + "]";
            end
          end
        end
        pc_out = "#{tag}: #{out_pc}" if(out_pc);
      end
    else
      if($point_counter["$nick,"])
        tag_arr = $point_counter["$nick,"];
        tag_out = "";
        tag_arr.each do |tag_o|
          check_name = "#{nick},,#{tag_o}";
          if($point_counter["#{check_name},0"])
            tag_out += "#{tag_o}(" + $point_counter["#{check_name},0"] + ") ";
          end
          if($point_counter["#{check_name},1"])
            tag_out += "#{tag_o}[" + $point_counter["#{check_name},1"] + "] ";
          end
        end
        if(tag_out)
          debug("中身があるなら")
          pc_out += ", " if(pc_out);
          pc_out += "#{tag_out}";
        end
      end
    end
    
    return pc_out
  end
  
  def getPointListAtSameChannel(tagName)
    debug("getPointListAtSameChannel(command, nick, channel, pointerMode, tagName)", @command, @nick, @channel, @pointerMode, tagName)
    debug("同一チャンネル特定タグ(ポイント)の表示")
    
    output = ""
    
    output << "#{tagName}:" unless( tagName.empty? )
                              
    debug("getPointListAtSameChannel @countInfos", @countInfos)
    characterInfoList = getCharacterInfoList
    
    characterInfoList.keys.sort.each do |characterName|
      characterInfo = characterInfoList[characterName]
      
      tagText = ''
      characterInfo.keys.sort.each do |currentTag|
        unless( tagName.empty? )
          next unless( tagName == currentTag )
        end
        
        info = characterInfo[currentTag]
        currentValue = info[:currentValue]
        maxValue = info[:maxValue]
        
        tagText << "#{currentValue}"
        tagText << "/#{maxValue}" unless( maxValue.nil? )
      end
      
      unless( tagText.empty? )
        output << " " unless( output.empty? )
        output << "#{characterName}(#{tagText})"
      end
    end
    
    return output
  end
  
=begin
####################          カウンタ削除         ########################
  def delete_point_list(command, nick)
    command = command.upcase
    nick = @nick
    output = "";
    
    if($command =~ /^#(([\w!]+)[:：])?DIED!(\s|$)/)
      target_pc = $2;
      target_pc = "$nick" if( not$target_pc);
        tree_pc = $point_counter[nick];
        if( tree_pc )
            if($target_pc != "ALL!")
                tree_tag = $point_counter["#{nick},#{target_pc}"];
                if($tree_tag)
                    my @tag_arr = split /,/, $tree_tag;
                  tag_arr.each do |tag_o|
                        delete $point_counter["#{nick},#{target_pc},#{tag_o},0"];
                        delete $point_counter["#{nick},#{target_pc},#{tag_o},1"];
                    end
                end
                delete $point_counter["#{nick},#{target_pc}"];
                my @pc_arr = split /,/, $tree_pc;
              my @pc_list;
              pc_arr.each do |pc_o|
                    push @pc_list, $pc_o if($pc_o != $target_pc);
                end
                if(@pc_list)
                    $point_counter["#{nick}"] = join ",", @pc_list;
                else
                    delete $point_counter["#{nick}"];
                end
                output += "$target_pc";
            else
                my @pc_arr = split /,/, $tree_pc;
              pc_arr.each do |pc_o|
                    tree_tag = $point_counter["#{nick},#{pc_o}"];
                    if($tree_tag)
                        my @tag_arr = split /,/, $tree_tag;
                      tag_arr.each do |tag_o|
                            delete $point_counter["#{nick},#{pc_o},#{tag_o},0"];
                            delete $point_counter["#{nick},#{pc_o},#{tag_o},1"];
                        end
                    end
                    delete $point_counter["#{nick},#{pc_o}"];
                    output += "," if(output);
                    output += "\L\u$pc_o";
                end
                delete $point_counter["#{nick}"];
            end
            unless( $point_counter["#{nick}"])   # 登録PCが無い=カウンタが全て無くなった
                d_chan = getPrintPlotChannel("#{nick}");
                if($d_chan)
                    tree_mem = $point_counter["#{d_chan}"];
                    if($tree_mem)
                        my @mem_arr = split /,/, $tree_mem;
                        my @new_mem;
                      mem_arr.each do |mem_o|
                            push @new_mem ,$mem_o if($mem_o != $nick);
                        end
                        if(scalar @new_mem)
                            $point_counter["#{d_chan}"] = join ",", @new_mem;
                        else
                            delete $point_counter["#{d_chan}"];
                        end
                    end
                end
            end
        end
    end
    return output; # 削除されたPC名(カンマ区切り)を返す
}

=end
####################          識別名の交換         ########################
  def rename_point_counter
    debug("rename_point_counter @command, @nick", @command, @nick)
    
    output = "1"
    
    return output unless( /^#RENAME!\s*(.+?)\s*\-\>\s*(.+?)(\s|$)/ =~ @command )
    
    oldName = $1;
    newName = $2;
    debug("oldName, newName", oldName, newName)
    
    # {:channelName => {:characterName => (カウンター情報) }
    characterInfoList = getCharacterInfoList(@channel)

    counterInfo = characterInfoList.delete(oldName)
    return output if( counterInfo.nil? )
    
    characterInfoList[newName] = counterInfo
    
    output = "#{oldName}->#{newName}";   # 変更メッセージ
    return output;
  end
  

####################          その他の処理         ########################

  def setPointCounters(nick, pc, target)
    key = "#{nick},#{pc}"
    setPointCounter(key, pc)
    
    key = "#{nick},#{pc},#{target}"
    setPointCounter(key, target)
  end
  
  def setPointCounter(key, data)
    debug("setPointCounter begin key, data", key, data)
    
    unless( $point_counter.include?(key) )
      debug("$point_counterにkeyが存在しないので新規作成")
      $point_counter[key] = data
      return
    end
    
    debug("$point_counterにkeyが存在する場合")
    
    cnt_list = $point_counter[key]
    unless( cnt_list.include?( data ) )
      cnt_list << data
    end
  end
  
  
  def sort_point_hash(base_hash)
    keys = base_hash.keys
    
    pc_sorted = keys.sort_by do |a, b|
      a_current, a_max = getPointHashCurrentAndMax(a)
      b_current, b_max = getPointHashCurrentAndMax(b)
      
      # 現在値が小さい方が後ろ、同じ時はダメージが大きい方が後ろ(後方が危険)
      
      compare = (b_crr <=> a_crr)
      if( compare == 0 )
        compare = (a_max <=> b_max)
        if( compare == 0 )
          compare = (a <=> b)
        end
      end
      
      compare
    end
    
    return pc_sorted;
  end
  
  def getPointHashCurrentAndMax(key)
    if(/(\d+)[\/](\d+)/ =~ key)
      current = $1;
      max = $2;
      return current, max
    end
    return 0, 0
  end



  
end
