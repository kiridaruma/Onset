#!C:\Program Files\Ruby-2.3-x64\bin\ruby.exe
#--*-coding:utf-8-*--

require 'cgi'
require './bcdiceCore.rb'
require './configBcDice.rb'

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

bcmaker = BCDiceMaker.new
bcdice = bcmaker.newBcDice

bcdice.setGameByTitle(params['sys'][0])
bcdice.setMessage(params['text'][0])
bcdice.setNick('onset')
hoge, foo = bcdice.dice_command

puts hoge