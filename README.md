# Onset!

Onset is simple online TRPG chat

version-2.0.2_あやなみ(ayanami)  
β版から、正式版に移行しました  
(ほとんど何も変わってません)  
  
##2.0.2の改善点  
Bone&CarsをVer2.02.54にアップデートしました  
また、PHP7環境下で一部動作しない点があったので、修正を行いました  
###テクニカルノート  
上の通り、PHP7で動作しない点があったので修正しました  
Onset.phpのsplit関数ですが、PHP7で削除されていて動かないので、explodeに変更しました  
(非推奨なのは知ってたけど、まさか削除とは...定期的にPHP.netとか見ないといけないですね)  
あとは、bcdiceをアップデートしました  
roll.rbは変更していません、クラスの継承って便利ですね  
  
今後追加予定の機能
+ 自動部屋削除機能
+ ネットワーク機能
