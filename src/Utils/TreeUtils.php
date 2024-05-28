<?php
namespace Yangliuxin\Utils\Utils;

class TreeUtils
{
    const TREE_CONFIG = [
        'primaryKey' => 'id',
        'parentKey' => 'parent_id',
        'titleKey' => 'title',
        'sortKey' => 'sort',
        'childrenKey' => 'children',
        'levelKey' => 'level',];

    public static function getTree($data, $pid = 0, $level = 1): array
    {
        $list = array();
        $childrenData = self::getChildrenData($data, $pid, $level);
        foreach ($childrenData as $key => $val) {

            $val[self::TREE_CONFIG['childrenKey']] = self::getTree($data, $val['id'], $level + 1);
            $val[self::TREE_CONFIG['levelKey']] = $level;
            $list[] = $val;
        }

        return $list;
    }

    private static function multiSort($arr, $key): array
    {
        array_multisort(array_column($arr, $key), SORT_ASC, $arr);
        return $arr;
    }

    public static function getChildrenData($data, $pid = 0, $level = 0): array
    {
        $list = [];
        foreach ($data as $key => $val) {
            if ($val[self::TREE_CONFIG['parentKey']] == $pid) {
                $list[] = $val;
            }
        }
        return self::multiSort($list, 'sort');
    }
}
