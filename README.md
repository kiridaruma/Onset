# Onset!

Onset is simple online TRPG chat

version-2.1.1_いよなみ(iyonami)  

## Onset!とは?  
手軽にTRPGができるチャット型TRPGオンラインセッションツールです  
__簡単に__、__素早く__、__軽く__ が売りで、複雑な機能などはありません。  
ただ、チャットができてダイスが振れるだけの軽量チャットです  
  
## 要求環境  
PHP >= 5.5  
Ruby >= 1.9  
  
[どどんとふ](https://github.com/torgtaitai/DodontoF)と同じダイスボットを使用しています  
([ボーンズ&カーズ](https://github.com/torgtaitai/bcdice)というダイスボットを内部で使用しています)  
また、Honokaというcssライブラリを使っているので、綺麗な日本語フォントとデザインでTRPGをプレイできます  
  
## 試してみたい？  
[kiridarumaサーバ](https://onset.kiridaruma.net)へどうぞ  
[こかげサーバ](https://cokage.works/onset/)や、[どどんとふ公式鯖サーバ](http://www2.taruki.com/Onset/)もあります  
上記三つのサーバは「開発者おすすめサーバ」です  
また、自分のサーバにOnset!を設置することもできます  
[ここからダウンロードできます](https://github.com/kiridaruma/Onset/releases/download/v2.1.0/Onset2.1.0.zip)  
~~詳しくはこちら~~ (現在作成中)  
  
Onset!を改造したいという方は、[wiki](https://github.com/kiridaruma/Onset/wiki)を読むといいかもしれません  
  
## 2.1.1の改善点  
バグ修正を行いました  
+ html特殊文字が入った部屋名の部屋に対してremoveRoomとloginができなかった不具合
+ チャットログ更新の際に同じ発言が二つ表示されたり、一部ログが消えたりする不具合
core.php内の関数に対して、タイプヒンティングを導入しました  

  
