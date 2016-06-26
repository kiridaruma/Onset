# -*- coding: utf-8 -*-

class DiceBot
  @@bcdice = nil
  
  @@DEFAULT_SEND_MODE = 2                  # デフォルトの送信形式(0=結果のみ,1=0+式,2=1+ダイス個別)

  
  def initialize
    @sendMode = @@DEFAULT_SEND_MODE #(0=結果のみ,1=0+式,2=1+ダイス個別)
    @sortType = 0      #ソート設定(1 = 足し算ダイスでソート有, 2 = バラバラロール（Bコマンド）でソート有, 3 = １と２両方ソート有）
    @sameDiceRerollCount = 0     #ゾロ目で振り足し(0=無し, 1=全部同じ目, 2=ダイスのうち2個以上同じ目)
    @sameDiceRerollType = 0   #ゾロ目で振り足しのロール種別(0=判定のみ, 1=ダメージのみ, 2=両方)
    @d66Type = 1        #d66の差し替え(0=D66無し, 1=順番そのまま([5,3]->53), 2=昇順入れ替え([5,3]->35)
    @isPrintMaxDice = false      #最大値表示
    @upplerRollThreshold = 0      #上方無限
    @unlimitedRollDiceType = 0    #無限ロールのダイス
    @rerollNumber = 0      #振り足しする条件
    @defaultSuccessTarget = ""      #目標値が空欄の時の目標値
    @rerollLimitCount = 10000    #振り足し回数上限
    @fractionType = "omit"     #端数の処理 ("omit"=切り捨て, "roundUp"=切り上げ, "roundOff"=四捨五入)
    
    @gameType = 'DiceBot'
  end
  
  attr_accessor :rerollLimitCount
  
  attr_reader :sendMode, :sameDiceRerollCount, :sameDiceRerollType, :d66Type
  attr_reader :isPrintMaxDice, :upplerRollThreshold, :unlimitedRollDiceType
  attr_reader :defaultSuccessTarget, :rerollNumber, :fractionType
  
  
  def info
    {
      'name' => gameName,
      'gameType' => gameType,
      'prefixs' => prefixs,
      'info' => getHelpMessage,
    }
  end
  
  def gameName
    gameType
  end
  
  def prefixs
    []
  end
  
  def gameType
    @gameType
  end
  
  def setGameType(type)
    @gameType = type
  end
  
  def setSendMode(m)
    @sendMode = m
  end
  
  def upplerRollThreshold=(v)
    @upplerRollThreshold = v
  end
  
  def bcdice=(b)
    @@bcdice = b
  end
  
  def bcdice
    @@bcdice
  end
  
  def rand(max)
    @@bcdice.rand(max)
  end
  
  def check_suc(*params)
    @@bcdice.check_suc(*params)
  end
  
  def roll(*args)
    @@bcdice.roll(*args)
  end
  
  def marshalSignOfInequality(*args)
    @@bcdice.marshalSignOfInequality(*args)
  end
  
  def unlimitedRollDiceType
    @@bcdice.unlimitedRollDiceType
  end
  
  def sortType
    @sortType
  end
  
  def setSortType(s)
    @sortType = s
  end
  
  
  def d66(*args)
    @@bcdice.getD66Value(*args)
  end
  
  def rollDiceAddingUp(*arg)
    @@bcdice.rollDiceAddingUp(*arg)
  end
  
  def getHelpMessage
    ''
  end
  
  def parren_killer(string)
    @@bcdice.parren_killer(string)
  end
  
  def changeText(string)
    debug("DiceBot.parren_killer_add called")
    string
  end
  
  def dice_command(string, nick_e)
    string = @@bcdice.getOriginalMessage if( isGetOriginalMessage )
    
    debug('dice_command Begin string', string)
    secret_flg = false
    
    prefixsRegText = prefixs.join('|')
    unless( /(^|\s)(S)?(#{prefixsRegText})(\s|$)/i =~ string )
      debug('not match in prefixs')
      return '1', secret_flg 
    end
    
    secretMarker = $2
    command = $3
    
    command = removeDiceCommandMessage(command)
    debug("dicebot after command", command)
    
    debug('match')
    
    output_msg, secret_flg = rollDiceCommandCatched(command)
    output_msg = '1' if( output_msg.nil? or output_msg.empty? )
    secret_flg ||= false
    
    output_msg = "#{nick_e}: #{output_msg}" if(output_msg != '1')
    
    if( secretMarker )   # 隠しロール
      secret_flg = true if(output_msg != '1')
    end
    
    return output_msg, secret_flg
  end
  
  #通常ダイスボットのコマンド文字列は全て大文字に強制されるが、
  #これを嫌う場合にはこのメソッドを true を返すようにオーバーライドすること。
  def isGetOriginalMessage
    false
  end
  
  def removeDiceCommandMessage(command)
    # "2d6 Atack" のAtackのようなメッセージ部分をここで除去
    command.sub(/[\s　].+/, '')
  end
  
  
  def rollDiceCommandCatched(command)
    result = nil
    begin
      debug('call rollDiceCommand command', command)
      result, secret_flg = rollDiceCommand(command)
    rescue => e
      debug("executeCommand exception", e.to_s, $@.join("\n"))
    end
    
    debug('rollDiceCommand result', result)
    
    return result, secret_flg
  end
  
  def rollDiceCommand(command)
    nil
  end

  
  def setDiceText(diceText)
    debug("setDiceText diceText", diceText)
    @diceText = diceText
  end
  
  def setDiffText(diffText)
    @diffText = diffText
  end
  
  def dice_command_xRn(string, nick_e)
    ''
  end
  
  def check_2D6(total_n, dice_n, signOfInequality, diff, dice_cnt, dice_max, n1, n_max)  # ゲーム別成功度判定(2D6)
    ''
  end
  
  def check_nD6(total_n, dice_n, signOfInequality, diff, dice_cnt, dice_max, n1, n_max) # ゲーム別成功度判定(nD6)
    ''
  end
  
  def check_nD10(total_n, dice_n, signOfInequality, diff, dice_cnt, dice_max, n1, n_max)# ゲーム別成功度判定(nD10)
    ''
  end
  
  def check_1D100(total_n, dice_n, signOfInequality, diff, dice_cnt, dice_max, n1, n_max)    # ゲーム別成功度判定(1d100)
    ''
  end

  def check_1D20(total_n, dice_n, signOfInequality, diff, dice_cnt, dice_max, n1, n_max)     # ゲーム別成功度判定(1d20)
    ''
  end
  
  
  def get_table_by_2d6(table)
    get_table_by_nD6(table, 2)
  end
  
  def get_table_by_1d6(table)
    get_table_by_nD6(table, 1)
  end
  
  def get_table_by_nD6(table, count)
    get_table_by_nDx(table, count, 6)
  end
  
  def get_table_by_nDx(table, count, diceType)
    num, = roll(count, diceType)
    
    text = getTableValue(table[num - count])
    
    return '1', 0  if( text.nil? )
    return text, num
  end
  
  
  def get_table_by_1d3(table)
    debug("get_table_by_1d3")

    count = 1
    num, = roll(count, 6)
    debug("num", num)
    
    index = ((num - 1)/ 2)
    debug("index", index)
    
    text = table[index]
    
    return '1', 0  if( text.nil? )
    return text, num
  end
  
  def getD66(isSwap)
    number = bcdice.getD66(isSwap)
  end
  
  # D66 ロール用（スワップ、たとえば出目が【６，４】なら「６４」ではなく「４６」とする
  def get_table_by_d66_swap(table)
    isSwap = true
    number = bcdice.getD66(isSwap)
    return get_table_by_number(number, table), number
  end
  
  # D66 ロール用
  def get_table_by_d66(table)
    dice1, dummy = roll(1, 6)
    dice2, dummy = roll(1, 6)
    
    num = (dice1 - 1) * 6 + (dice2 - 1)
    
    text = table[num]
    
    indexText = "#{dice1}#{dice2}"
    
    return '1', indexText  if( text.nil? )
    return text, indexText
  end
  
  
  
  #ダイスロールによるポイント等の取得処理用（T&T悪意、ナイトメアハンター・ディープ宿命、特命転校生エクストラパワーポイントなど）
  def getDiceRolledAdditionalText(n1, n_max, dice_max)
    ''
  end
  
  #ダイス目による補正処理（現状ナイトメアハンターディープ専用）
  def getDiceRevision(n_max, dice_max, total_n)
    return '', 0
  end
  
  #ダイス目文字列からダイス値を変更する場合の処理（現状クトゥルフ・テック専用）
  def changeDiceValueByDiceText(dice_now, dice_str, isCheckSuccess, dice_max)
    dice_now
  end
  
  #SW専用
  def setRatingTable(nick_e, tnick, channel_to_list)
    '1'
  end
  
  #振り足し時のダイス読み替え処理用（ダブルクロスはクリティカルでダイス10に読み替える)
  def getJackUpValueOnAddRoll(dice_n)
    0
  end

  #ガンドッグのnD9専用
  def isD9
    false
  end
  
  #シャドウラン4版用グリッチ判定
  def getGrichText(numberSpot1, dice_cnt_total, suc)
    ''
  end
  
  # SW2.0 の超成功用
  def check2dCritical(critical, dice_new, dice_arry)
  end
  
  def is2dCritical
    false
  end
  
  def getDiceList
    getDiceListFromDiceText(@diceText)
  end
  
  def getDiceListFromDiceText(diceText)
    debug("getDiceList diceText", diceText)
    
    diceList = []
    
    if( /\[([\d,]+)\]/ =~ diceText )
      diceText = $1
    end
    
    return diceList unless( /([\d,]+)/ =~ diceText )
    
    diceString = $1
    diceList = diceString.split(/,/).collect{|i| i.to_i}
    
    debug("diceList", diceList)
    
    return diceList
  end

  
  #** 汎用表サブルーチン
  def get_table_by_number(index, table, default = '1')
    table.each do |item|
      number = item[0]
      if( number >= index )
        return getTableValue( item[1] )
      end
    end
    
    return getTableValue( default )
  end
  
  def getTableValue(data)
    if( data.kind_of?( Proc ) )
      return data.call()
    end
    
    return data
  end
  
  
end
