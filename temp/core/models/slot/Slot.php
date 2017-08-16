<?php

class Slot extends BaseModel {

    public static $resourceName = 'Slot';

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'game_name_cn';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'game_name_en',
        'game_name_cn',
        'platform',
        'provider',
//        'category',
        'game_code',
//        'category',
        'category_label',
        'group_label',
        'theme_label',
        'rtp',
        'jackpot_amount',
        'is_enable',
        'is_day_commission',
        'is_jackpot',
        'is_available_bet',
        'progressive_game_code2',
        'sequence',
        'is_mobile',
    ];

    public static $sequencable = true;

    public static $ignoreColumnsInView = [
        'category'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'desc'
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'game_name_en' => 'required|max:50',
        'game_name_cn' => 'required|max:50',
        'game_code' => 'required|max:50',
        'platform' => '',
        'category' => '',
        'group' => '',
        'theme' => '',
        'rtp' => '',
        'is_enable' => 'in:0,1',
        'is_day_commission' => 'in:0,1',
        'is_jackpot' => 'in:0,1',
        'is_available_bet' => 'in:0,1',
        'is_mobile' => 'in:0,1,2',
        'sub_game_code' => '',
        'progressive_game_code2' => '',
        'sequence' => '',
    ];
    protected $table = 'slots';
    public static $htmlSelectColumns = [
        'category' => 'aCategoriesTree',
        'group' => 'aGroupsTree',
        'theme' => 'aThemesTree',
        'is_mobile' => 'aDisplays',
    ];

    const PLATFORM_PT = 0;
    const PLATFORM_AG = 1;
    const PLATFORM_IM = 2;
    const PLATFORM_MAIN = 3;
    const PLATFORM_HB = 4;
    const PLATFORM_MG = 5;
    public static $aPlatforms = [
        self::PLATFORM_PT => 'pt',
        self::PLATFORM_AG => 'ag',
        self::PLATFORM_IM => 'im',
//        self::PLATFORM_MAIN => 'main',
        self::PLATFORM_HB => 'hb',
        self::PLATFORM_MG => 'mg',
    ];

    const PROVIDER_PT = 1;
    const PROVIDER_TTG = 2;
    const PROVIDER_GOS = 3;
    const PROVIDER_PRG = 4;
    const PROVIDER_INT = 5;
    const PROVIDER_SPG = 6;
    const PROVIDER_AG = 7;
    const PROVIDER_HB = 8;
    const PROVIDER_MG = 9;
    public static $aProviders = [
        self::PROVIDER_PT => 'pt',
        self::PROVIDER_TTG => 'ttg',
        self::PROVIDER_GOS => 'gos',
        self::PROVIDER_PRG => 'prg',
        self::PROVIDER_AG => 'ag',
        self::PROVIDER_HB => 'hb',
        self::PROVIDER_MG => 'mg',
    ];

    //游戏显示条件
    const DISPLAY_PC = 0;
    const DISPLAY_MOBILE = 1;
    const DISPLAY_BOTH = 2;
    public static $aDisplays = [
        self::DISPLAY_PC => 'pc',
        self::DISPLAY_MOBILE => 'mobile',
        self::DISPLAY_BOTH => 'both',
    ];

    public static $aImProviders = ['Ttg', 'Gos', 'Prg', 'Int', 'Spg', 'Other'];

    protected $fillable = [
        'id',
        'platform',
        'game_name_en',
        'game_name_cn',
        'game_code',
        'category',
        'group',
        'theme',
        'rtp',
        'is_enable',
        'is_day_commission',
        'progressive_share',
        'initial_seed_reseed_value',
        'bet_size',
        'progressive_game_group',
        'progressive_game_code2',
        'technology',
        'style',
        'max_bet',
        'min_bet',
        'jackpot_name',
        'progressive_jackpot',
        'remarks',
        'lines',
        'free_play',
        'game_type',
        'category',
        'provider',
        'jackpot_amount',
        'is_jackpot',
        'sub_game_code',
        'is_available_bet',
        'is_mobile',
        'created_at',
        'updated_at',
    ];

    protected function getCategoryLabelAttribute(){
        return $this->getLabel('category', SlotCategory::TYPE_CATEGORY);
    }

    protected function getGroupLabelAttribute(){
        return $this->getLabel('group', SlotCategory::TYPE_GROUP);
    }
    protected function getThemeLabelAttribute(){
        return $this->getLabel('theme', SlotCategory::TYPE_THEME);
    }

    private function getLabel($sField, $iType){
        if(!$this->attributes[$sField]){
            return '';
        }
        $aTree = & SlotCategory::getTitleList($iType, true);
        $aIds = explode(',', $this->attributes[$sField]);
        $aLabel = [];
        foreach ($aIds as $iId){
            $aLabel[] = $aTree[$iId];
        }
        return implode(',', $aLabel);
    }
}
