<?php
namespace libs;

use Slot;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class Games {
    private static $pagesize = 1000;

    public static function generateFindInSet($a, $sField){
        $sRaw = '(';
        $aRaw = [];
        foreach ($a as $i) {
            $aRaw[] = 'find_in_set('.$i.', slots.'.$sField.')';
        }
        $sRaw .= implode(' or ', $aRaw);
        $sRaw .= ')';
        return $sRaw;
    }

    /**
     * 根据索引查询游戏
     * @return mixed
     */
    public static function gamesSearch(
        $sName = null,
        $aPlatform = null,
        $fMinBet = null,
        $aGroup = null,
        $aCategory = null,
        $sLines = null
    ){
        // 游戏是否可用
        $oQuery = Slot::where('is_enable', 1);
        // echo $oQuery->toSql();

        // 按名字搜索
        if (isset($sName) && $sName) {
            $oQuery = $oQuery->whereRaw("(game_name_cn like '%".$sName."%' or game_name_en like '%".$sName."%')");
        }

        // 按平台搜索
        if (isset($aPlatform) && !empty($aPlatform)) {
            $aPlatform = isset($aPlatform[0]) && $aPlatform[0] == 'gos,prg' ? ['Gos', 'Prg'] : $aPlatform;
            $aPlatform = array_filter($aPlatform);
            $oQuery = $oQuery->whereIn('provider', $aPlatform);
        }

        // 字段暂时无用
        if (isset($fMinBet) && $fMinBet) {
            $oQuery = $oQuery->where('min_bet', $fMinBet);
        }

        // 按组别进行搜索
        if (isset($aGroup) && !empty($aGroup)) {
            $aGroup = array_filter($aGroup);
            $sGroupRaw = self::generateFindInSet($aGroup, 'group');
            $oQuery = $oQuery->whereRaw($sGroupRaw);
        }

        // 按趣拍分类进行搜索
        if (isset($aCategory) && !empty($aCategory)) {
            $aCategory = array_filter($aCategory);
            $sCategoryRaw = self::generateFindInSet($aCategory, 'category');
            $oQuery = $oQuery->whereRaw($sCategoryRaw);
        }

        // 按卷轴数进行搜索
        if (isset($sLines) && $sLines && strpos($sLines, '-') > 0) {
            $aLinesRang = explode('-', $sLines);
            if ($aLinesRang[0]) $oQuery = $oQuery->where('lines', '>=', $aLinesRang[0]);
            if ($aLinesRang[1]) $oQuery = $oQuery->where('lines', '>=', $aLinesRang[1]);
        }

        $oQuery = $oQuery->whereIn('is_mobile', [Slot::DISPLAY_MOBILE, Slot::DISPLAY_BOTH]);

        $aSlots = $oQuery->orderBy('sequence', 'desc')->paginate(static::$pagesize);

        if ($aSlots->count() <= 0) {
            $bNotFound = true;
            $aSlots = Slot::where('is_enable', 1);
            $aSlots = $aSlots->whereIn('is_mobile', [Slot::DISPLAY_BOTH, Slot::DISPLAY_MOBILE]);

            $aSlots = $aSlots->orderBy('sequence', 'desc')->paginate(static::$pagesize);
        }

        return $aSlots->toArray();
    }
}
