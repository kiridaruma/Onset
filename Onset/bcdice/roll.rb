#!/usr/local/bin/ruby -Ku
#--*-coding:utf-8-*--

$LOAD_PATH << File.dirname(__FILE__)

require 'cgi'
require 'bcdiceCore.rb'
require 'configBcDice.rb'

class OnsetBCDiceMaker < BCDiceMaker
  
  def newBcDice
    bcdice = OnsetBCDice.new(self, @cardTrader, @diceBot, @counterInfos, @tableFileData)
    return bcdice
  end
end

class OnsetBCDice < BCDice
  
  def setNick(nick)
    @nick_e = nick
  end
end

puts "Content-Type: text/plain\n\n"

cgi = CGI.new();
params = cgi.params

if(params['list'][0] == "1")
  $allGameTypes.each do |var|
    puts var + "\n"
  end
  exit
end

unless($allGameTypes.include?(params['sys'][0]) || params['sys'][0] == 'None')
  puts 'error'
  exit
end

if(params['sys'][0] == nil)
  params['sys'][0] == "None"
end

bcmaker = OnsetBCDiceMaker.new
bcdice = bcmaker.newBcDice()

bcdice.setGameByTitle(params['sys'][0])
bcdice.setMessage(params['text'][0])
bcdice.setNick('onset')
hoge, foo = bcdice.dice_command

puts hoge
