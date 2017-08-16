<?php

class SlotCategory extends BaseModel {

    public static $resourceName = 'SlotCategory';

    const TYPE_CATEGORY = 1;
    const TYPE_GROUP = 2;
    const TYPE_THEME = 3;

    public static $aType = [
        self::TYPE_GROUP => 'group',
        self::TYPE_CATEGORY => 'category',
        self::TYPE_THEME => 'theme',
    ];

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'name';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'name',
        'type',
        'is_display',
    ];
    public static $treeable = true;

    /**
     * the main param for index page
     * @var string 
     */
    public static $mainParamColumn = 'parent_id';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:50',
        'type' => 'required',
        'is_display' => 'in:0,1',
    ];
    protected $table = 'slot_categories';
    public static $htmlSelectColumns = [
        'type' => 'aType',
    ];
    
    
    protected $fillable = [
        'name',
        'type',
        'is_display',
    ];

    public static $viewColumnMaps = [
        'games_set' => 'formatted_games_set',
    ];

    public static function getCmsCategoryTypes($parent_id) {
        $aTypes = self::where('parent_id', $parent_id)->orWhere('id', $parent_id)->get(['id','name'])->toArray();
        return $aTypes;
    }

    /**
     * 返回数据列表
     * @param boolean $bOrderByTitle
     * @return array &  键为ID，值为$$titleColumn
     */
    public static function & getTitleList($iType = SlotCategory::TYPE_GROUP, $bAll = false, $bOrderByTitle = true) {
        $aColumns = [ 'id', static::$titleColumn];
        $sOrderColumn = $bOrderByTitle ? static::$titleColumn : 'id';
        $oModels = self::where('type', $iType);
        if (!$bAll)
            $oModels = $oModels->where('is_display', 1);
        $oModels = $oModels->orderBy($sOrderColumn, 'asc')->get($aColumns);
        $data = [];
        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel->{static::$titleColumn};
        }
        return $data;
    }

    protected function getFormattedGamesSetAttribute(){
        if(!$this->attributes['games_set']){
            return '';
        }
        $aGameSets = [];
        foreach(json_decode($this->attributes['games_set'], true) as $aGameSet){
            $aGameSets[] = array_only($aGameSet, ['game_code'])['game_code'];
        }
        return implode(',', $aGameSets);
    }

}
