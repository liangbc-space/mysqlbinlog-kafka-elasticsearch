<?php

namespace app\models\elasticsearch;


use commons\Commons;
use framework\elasticsearch\ElasticsearchModel;

class GoodsBase extends ElasticsearchModel
{

    protected static $index = 'goods_base_test';

    protected static $alias = 'alias_goods_base';


    public static function getIndex()
    {
        return self::$index;
    }

    public static function getAlias()
    {
        return self::$alias;
    }

    public static function _id($storeId, $goodsId)
    {
        return $storeId . '-' . $goodsId;
    }


    public static function format($goodsInfo)
    {
        isset($goodsInfo['id']) && $goodsInfo['id'] = Commons::toInteger($goodsInfo['id']);

        isset($goodsInfo['store_id']) && $goodsInfo['store_id'] = Commons::toInteger($goodsInfo['store_id']);

        isset($goodsInfo['uniqueeid']) && $goodsInfo['uniqueeid'] = Commons::toString($goodsInfo['uniqueeid']);

        isset($goodsInfo['mysql_table_name']) && $goodsInfo['mysql_table_name'] = Commons::toString($goodsInfo['mysql_table_name']);

        isset($goodsInfo['base_name']) && $goodsInfo['base_name'] = Commons::toString($goodsInfo['base_name']);

        isset($goodsInfo['search_keywords']) && $goodsInfo['search_keywords'] = Commons::toString($goodsInfo['search_keywords']);

        isset($goodsInfo['brand_id']) && $goodsInfo['brand_id'] = Commons::toInteger($goodsInfo['brand_id']);

        isset($goodsInfo['goods_type_id']) && $goodsInfo['goods_type_id'] = Commons::toInteger($goodsInfo['goods_type_id']);

        isset($goodsInfo['category_path']) && $goodsInfo['category_path'] = Commons::toString($goodsInfo['category_path']);

        isset($goodsInfo['category_id']) && $goodsInfo['category_id'] = Commons::toInteger($goodsInfo['category_id']);

        isset($goodsInfo['category_ids']) && $goodsInfo['category_ids'] = Commons::toInteger($goodsInfo['category_ids']);

        isset($goodsInfo['category_name']) && $goodsInfo['category_name'] = Commons::toString($goodsInfo['category_name']);

        isset($goodsInfo['category_names']) && $goodsInfo['category_names'] = Commons::toString($goodsInfo['category_names']);

        isset($goodsInfo['codeno']) && $goodsInfo['codeno'] = Commons::toString($goodsInfo['codeno']);

        isset($goodsInfo['image']) && $goodsInfo['image'] = Commons::toString($goodsInfo['image']);

        isset($goodsInfo['price']) && $goodsInfo['price'] = Commons::toInteger($goodsInfo['price']);

        isset($goodsInfo['cost_price']) && $goodsInfo['cost_price'] = Commons::toDecimal($goodsInfo['cost_price']);

        isset($goodsInfo['market_price']) && $goodsInfo['market_price'] = Commons::toDecimal($goodsInfo['market_price']);

        isset($goodsInfo['listorder']) && $goodsInfo['listorder'] = Commons::toInteger($goodsInfo['listorder']);

        isset($goodsInfo['status']) && $goodsInfo['status'] = Commons::toInteger($goodsInfo['status']);

        isset($goodsInfo['up_time']) && $goodsInfo['up_time'] = Commons::toInteger($goodsInfo['up_time']);

        isset($goodsInfo['down_time']) && $goodsInfo['down_time'] = Commons::toInteger($goodsInfo['down_time']);

        isset($goodsInfo['create_time']) && $goodsInfo['create_time'] = Commons::toInteger($goodsInfo['create_time']);

        isset($goodsInfo['modify_time']) && $goodsInfo['modify_time'] = Commons::toInteger($goodsInfo['modify_time']);

        isset($goodsInfo['template_page']) && $goodsInfo['template_page'] = Commons::toString($goodsInfo['template_page']);

        isset($goodsInfo['visit_counts']) && $goodsInfo['visit_counts'] = Commons::toInteger($goodsInfo['visit_counts']);

        isset($goodsInfo['buy_counts']) && $goodsInfo['buy_counts'] = Commons::toInteger($goodsInfo['buy_counts']);

        isset($goodsInfo['wishlist_counts']) && $goodsInfo['wishlist_counts'] = Commons::toInteger($goodsInfo['wishlist_counts']);

        isset($goodsInfo['comment_counts']) && $goodsInfo['comment_counts'] = Commons::toInteger($goodsInfo['comment_counts']);

        isset($goodsInfo['comment_value']) && $goodsInfo['comment_value'] = Commons::toInteger($goodsInfo['comment_value']);

        isset($goodsInfo['stock_nums']) && $goodsInfo['stock_nums'] = Commons::toInteger($goodsInfo['stock_nums']);

        isset($goodsInfo['sale_mode']) && $goodsInfo['sale_mode'] = Commons::toInteger($goodsInfo['sale_mode']);

        isset($goodsInfo['spec_mode']) && $goodsInfo['spec_mode'] = Commons::toInteger($goodsInfo['spec_mode']);

        isset($goodsInfo['is_diy_remark']) && $goodsInfo['is_diy_remark'] = Commons::toInteger($goodsInfo['is_diy_remark']);

        isset($goodsInfo['weight']) && $goodsInfo['weight'] = Commons::toDecimal($goodsInfo['weight']);

        isset($goodsInfo['start_time']) && $goodsInfo['start_time'] = Commons::toInteger($goodsInfo['start_time']);

        isset($goodsInfo['end_time']) && $goodsInfo['end_time'] = Commons::toInteger($goodsInfo['end_time']);

        isset($goodsInfo['is_free_shipping']) && $goodsInfo['is_free_shipping'] = Commons::toInteger($goodsInfo['is_free_shipping']);

        isset($goodsInfo['special_offer_id']) && $goodsInfo['special_offer_id'] = Commons::toInteger($goodsInfo['special_offer_id']);

        isset($goodsInfo['discount']) && $goodsInfo['discount'] = Commons::toDecimal($goodsInfo['discount']);

        isset($goodsInfo['title']) && $goodsInfo['title'] = Commons::toString($goodsInfo['title']);

        isset($goodsInfo['keywords']) && $goodsInfo['keywords'] = Commons::toString($goodsInfo['keywords']);

        isset($goodsInfo['descript']) && $goodsInfo['descript'] = Commons::toString($goodsInfo['descript']);

        isset($goodsInfo['mini_detail']) && $goodsInfo['mini_detail'] = Commons::toString($goodsInfo['mini_detail']);

        isset($goodsInfo['group_codeno']) && $goodsInfo['group_codeno'] = Commons::toString($goodsInfo['group_codeno']);

        isset($goodsInfo['moq']) && $goodsInfo['moq'] = Commons::toInteger($goodsInfo['moq']);

        isset($goodsInfo['mxoq']) && $goodsInfo['mxoq'] = Commons::toInteger($goodsInfo['mxoq']);

        isset($goodsInfo['is_bookable']) && $goodsInfo['is_bookable'] = Commons::toInteger($goodsInfo['is_bookable']);

        isset($goodsInfo['b2b_status']) && $goodsInfo['b2b_status'] = Commons::toInteger($goodsInfo['b2b_status']);

        isset($goodsInfo['user_group_ids']) && $goodsInfo['user_group_ids'] = Commons::toInteger($goodsInfo['user_group_ids']);

        isset($goodsInfo['user_group_id_values']) && $goodsInfo['user_group_id_values'] = Commons::toString($goodsInfo['user_group_id_values']);

        isset($goodsInfo['volume']) && $goodsInfo['volume'] = Commons::toDecimal($goodsInfo['volume']);

        isset($goodsInfo['supplier_remark']) && $goodsInfo['supplier_remark'] = Commons::toString($goodsInfo['supplier_remark']);

        isset($goodsInfo['video']) && $goodsInfo['video'] = Commons::toString($goodsInfo['video']);

        isset($goodsInfo['is_instock']) && $goodsInfo['is_instock'] = Commons::toInteger($goodsInfo['is_instock']);

        isset($goodsInfo['create_day']) && $goodsInfo['create_day'] = Commons::toString($goodsInfo['create_day']);

        isset($goodsInfo['brand_name']) && $goodsInfo['brand_name'] = Commons::toString($goodsInfo['brand_name']);

        isset($goodsInfo['tag_ids']) && $goodsInfo['tag_ids'] = Commons::toInteger($goodsInfo['tag_ids']);

        isset($goodsInfo['tag_names']) && $goodsInfo['tag_names'] = Commons::toString($goodsInfo['tag_names']);

        isset($goodsInfo['rec_ids']) && $goodsInfo['rec_ids'] = Commons::toInteger($goodsInfo['rec_ids']);

        isset($goodsInfo['rec_names']) && $goodsInfo['rec_names'] = Commons::toString($goodsInfo['rec_names']);

        isset($goodsInfo['images_other']) && $goodsInfo['images_other'] = Commons::toString($goodsInfo['images_other']);

        isset($goodsInfo['main_prop_name']) && $goodsInfo['main_prop_name'] = Commons::toString($goodsInfo['main_prop_name']);

        isset($goodsInfo['main_prop_image']) && $goodsInfo['main_prop_image'] = Commons::toString($goodsInfo['main_prop_image']);

        isset($goodsInfo['property_ids']) && $goodsInfo['property_ids'] = Commons::toInteger($goodsInfo['property_ids']);

        return $goodsInfo;
    }


}