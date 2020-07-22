<?php

namespace app\models\elasticsearch;


use commons\Commons;
use framework\ElasticsearchModel;

class GoodsBase extends ElasticsearchModel
{

    protected static $index = 'ymcart_goods_base';

    public static function getIndex()
    {
        return self::$index;
    }

    public static function _id($storeId, $goodsId)
    {
        return $storeId . '-' . $goodsId;
    }


    public static function format($goodsInfo)
    {
        isset($goodsInfo['id']) && $goodsInfo['id'] = Commons::stringToInteger($goodsInfo['id']);

        isset($goodsInfo['store_id']) && $goodsInfo['store_id'] = Commons::stringToInteger($goodsInfo['store_id']);

        isset($goodsInfo['base_name']) && $goodsInfo['base_name'] = strval($goodsInfo['base_name']);

        isset($goodsInfo['brand_id']) && $goodsInfo['brand_id'] = Commons::stringToInteger($goodsInfo['brand_id']);

        isset($goodsInfo['goods_type_id']) && $goodsInfo['goods_type_id'] = Commons::stringToInteger($goodsInfo['goods_type_id']);

        isset($goodsInfo['category_path']) && $goodsInfo['category_path'] = strval($goodsInfo['category_path']);

        isset($goodsInfo['category_id']) && $goodsInfo['category_id'] = Commons::stringToInteger($goodsInfo['category_id']);

        isset($goodsInfo['category_ids']) && $goodsInfo['category_ids'] = Commons::stringToInteger($goodsInfo['category_ids']);

        isset($goodsInfo['price']) && $goodsInfo['price'] = doubleval($goodsInfo['price']);

        isset($goodsInfo['cost_price']) && $goodsInfo['cost_price'] = doubleval($goodsInfo['cost_price']);

        isset($goodsInfo['market_price']) && $goodsInfo['market_price'] = doubleval($goodsInfo['market_price']);

        isset($goodsInfo['listorder']) && $goodsInfo['listorder'] = Commons::stringToInteger($goodsInfo['listorder']);

        isset($goodsInfo['status']) && $goodsInfo['status'] = Commons::stringToInteger($goodsInfo['status']);

        isset($goodsInfo['up_time']) && $goodsInfo['up_time'] = Commons::stringToInteger($goodsInfo['up_time']);

        isset($goodsInfo['down_time']) && $goodsInfo['down_time'] = Commons::stringToInteger($goodsInfo['down_time']);

        isset($goodsInfo['create_time']) && $goodsInfo['create_time'] = Commons::stringToInteger($goodsInfo['create_time']);

        isset($goodsInfo['modify_time']) && $goodsInfo['modify_time'] = Commons::stringToInteger($goodsInfo['modify_time']);

        isset($goodsInfo['visit_counts']) && $goodsInfo['visit_counts'] = Commons::stringToInteger($goodsInfo['visit_counts']);

        isset($goodsInfo['buy_counts']) && $goodsInfo['buy_counts'] = Commons::stringToInteger($goodsInfo['buy_counts']);

        isset($goodsInfo['wishlist_counts']) && $goodsInfo['wishlist_counts'] = Commons::stringToInteger($goodsInfo['wishlist_counts']);

        isset($goodsInfo['comment_counts']) && $goodsInfo['comment_counts'] = Commons::stringToInteger($goodsInfo['comment_counts']);

        isset($goodsInfo['comment_value']) && $goodsInfo['comment_value'] = Commons::stringToInteger($goodsInfo['comment_value']);

        isset($goodsInfo['stock_nums']) && $goodsInfo['stock_nums'] = Commons::stringToInteger($goodsInfo['stock_nums']);

        isset($goodsInfo['sale_mode']) && $goodsInfo['sale_mode'] = Commons::stringToInteger($goodsInfo['sale_mode']);

        isset($goodsInfo['spec_mode']) && $goodsInfo['spec_mode'] = Commons::stringToInteger($goodsInfo['spec_mode']);

        isset($goodsInfo['is_diy_remark']) && $goodsInfo['is_diy_remark'] = Commons::stringToInteger($goodsInfo['is_diy_remark']);

        isset($goodsInfo['weight']) && $goodsInfo['weight'] = doubleval($goodsInfo['weight']);

        isset($goodsInfo['start_time']) && $goodsInfo['start_time'] = Commons::stringToInteger($goodsInfo['start_time']);

        isset($goodsInfo['end_time']) && $goodsInfo['end_time'] = Commons::stringToInteger($goodsInfo['end_time']);

        isset($goodsInfo['is_free_shipping']) && $goodsInfo['is_free_shipping'] = Commons::stringToInteger($goodsInfo['is_free_shipping']);

        isset($goodsInfo['special_offer_id']) && $goodsInfo['special_offer_id'] = Commons::stringToInteger($goodsInfo['special_offer_id']);

        isset($goodsInfo['discount']) && $goodsInfo['discount'] = doubleval($goodsInfo['discount']);

        isset($goodsInfo['title']) && $goodsInfo['title'] = strval($goodsInfo['title']);

        isset($goodsInfo['special_offer_id']) && $goodsInfo['special_offer_id'] = Commons::stringToInteger($goodsInfo['special_offer_id']);

        isset($goodsInfo['moq']) && $goodsInfo['moq'] = Commons::stringToInteger($goodsInfo['moq']);

        isset($goodsInfo['mxoq']) && $goodsInfo['mxoq'] = Commons::stringToInteger($goodsInfo['mxoq']);

        isset($goodsInfo['is_bookable']) && $goodsInfo['is_bookable'] = Commons::stringToInteger($goodsInfo['is_bookable']);

        isset($goodsInfo['b2b_status']) && $goodsInfo['b2b_status'] = Commons::stringToInteger($goodsInfo['b2b_status']);

        isset($goodsInfo['user_group_ids']) && $goodsInfo['user_group_ids'] = Commons::stringToInteger($goodsInfo['user_group_ids']);

        isset($goodsInfo['volume']) && $goodsInfo['volume'] = doubleval($goodsInfo['volume']);

        isset($goodsInfo['is_instock']) && $goodsInfo['is_instock'] = Commons::stringToInteger($goodsInfo['is_instock']);

        isset($goodsInfo['tag_ids']) && $goodsInfo['tag_ids'] = Commons::stringToInteger($goodsInfo['tag_ids']);

        isset($goodsInfo['rec_ids']) && $goodsInfo['rec_ids'] = Commons::stringToInteger($goodsInfo['rec_ids']);

        isset($goodsInfo['property_ids']) && $goodsInfo['property_ids'] = Commons::stringToInteger($goodsInfo['property_ids']);

        return $goodsInfo;
    }


}