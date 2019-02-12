<?php
/*
Plugin Name: Booklist by digitalesia
Plugin URI: https://digitalesia.com/
Description: Showing Booklist
Version: 1.0
Author: Digitalesia
Author URI: https://digitalesia.com/
*/

class Booklist{
    public function __construct() {
        add_shortcode( 'bl_listnew', array( $this, 'booklist_new_shortcode' ) );
        add_shortcode( 'bl_listrecomended', array( $this, 'booklist_recomended_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'booklist_enqueue' ) );
        add_filter( 'rwmb_meta_boxes', array( $this, 'booklist_metabox' ) );
    }

    function booklist_metabox( $meta_boxes ) {
        $prefix = 'booklist_';
        $meta_boxes[] = array(
            'id'         => 'booklist',
            'title'      => 'Book Price',
            'post_types' => 'post',
            'context'    => 'side',
            'priority'   => 'high',

            'fields' => array(
                array(
                    'name'  => 'Author',
                    'desc'  => 'author of the book',
                    'id'    => $prefix . 'author',
                    'type'  => 'text',
                    'std'   => 'oleh :'
                ),
                array(
                    'name'  => 'Normal Price',
                    'desc'  => 'in rupiah',
                    'id'    => $prefix . 'normal_price',
                    'type'  => 'text',
                ),
                array(
                    'name'  => 'Discount Price',
                    'desc'  => 'in rupiah',
                    'id'    => $prefix . 'discount_price',
                    'type'  => 'text',
                ),
            )
        );

        return $meta_boxes;
    }

    public function booklist_enqueue(){
        wp_enqueue_style( 'bootstrap' , 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css', false );
        wp_enqueue_script( 'bootstrap-js' , 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array(),null, true );
    }

    public function booklist_new_shortcode(){
        ob_start();
        $this->booklist_show_latest();
        return ob_get_clean();
    }

    public function booklist_show_latest(){
        global $wpdb;
        $latest_book = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}UPCP_Items ORDER BY Item_Date_Created DESC LIMIT 12 ");
        ?>
        <style>
            .booklist-book-detail{
                margin-bottom:0;
                word-break: break-word;
            }
            .booklist-latest{
                margin-bottom:15px;
            }
            .booklist-thumbnail-container{
                height:230px;
                width:160px;
                background-color:transparent;
                text-align:center;
                white-space:nowrap;
            }
            .booklist-helper{
                display: inline-block;
                height:100%;
                vertical-align:middle;
            }
            .booklist-thumbnail{
                max-height:230px;
                max-width:160px;
                vertical-align: middle;
            }
        </style>
        <div class="title">
            <h1>Buku Terbaru</h1>
        <hr>
        </div>
        <div class="row booklist-latest">
        <?php 
        foreach ( $latest_book as $post ) :
            $title          = $post->Item_Name;
            $title_arr      = explode(" ",$title );

            $word_length=0;
            foreach( $title_arr as $key => $word ){
                $word_length += strlen( $word );
                if( $word_length>30) {
                    $word_more_than_30 = true;
                    $title = implode(" ", array_slice( $title_arr, 0, $key-1 ) ).'...';
                    break;
                } 
            }
            $url            = get_site_url()."/katalog-gading/?&SingleProduct=".$post->Item_ID;
            $thumbnail      = $post->Item_Photo_URL;
            $normal_price   = $post->Item_Price;
            $discount_price = $post->Item_Sale_Price;
            ?>
            <div class="col-2" style="text-align:center;">
                <div class="booklist-thumbnail-container">
                <span class="booklist-helper"></span><img class="booklist-thumbnail" src="<?php echo $thumbnail;?>">
                </div>
                <h6><a href="<?php echo $url;?>" title="<?php echo esc_attr( $post->Item_Name );?>"><?php echo esc_html( $title ); ?></a></h6>
                <p class="booklist-book-detail"><?php echo esc_html( $author ); ?></p>
                <?php if( $discount_price != "" ) :?> 
                    <p class="booklist-book-detail"><del><?php echo esc_html( $normal_price ); ?></del></p>
                    <p class="booklist-book-detail"><?php echo esc_html( $discount_price ); ?></p>
                <?php else :?>
                    <p class="booklist-book-detail"><?php echo esc_html( $normal_price ); ?></p>
                <?php endif;?>
            </div>
        <?php endforeach; ?>
        </div> <?php
    }

    public function booklist_recomended_shortcode(){
        ob_start();
        $this->booklist_show_recomended();
        return ob_get_clean();
    }

    public function booklist_show_recomended() {
        global $wpdb;
        $recomended_book = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}UPCP_Items WHERE SubCategory_Name = 'Recomended' ORDER BY RAND() DESC LIMIT 12 ");
        ?>
        <style>
            .booklist-book-detail{
                margin-bottom:0;
                word-break: break-word;
            }
            .booklist-recomended{
                margin-bottom:15px;
            }
            .booklist-thumbnail-container{
                height:230px;
                width:160px;
                background-color:transparent;
                text-align:center;
                white-space:nowrap;
            }
            .booklist-helper{
                display: inline-block;
                height:100%;
                vertical-align:middle;
            }
            .booklist-thumbnail{
                max-height:230px;
                max-width:160px;
                vertical-align: middle;
            }
        </style>
        <div class="title">
            <h1>Buku Rekomendasi</h1>
        </div>
        <hr>
        <div class="row booklist-recomended">
        <?php 
        foreach ( $recomended_book as $post ) :
            $title          = $post->Item_Name;
            $title_arr      = explode(" ",$title );

            $word_length=0;
            foreach( $title_arr as $key => $word ){
                $word_length += strlen( $word );
                if( $word_length>30) {
                    $word_more_than_30 = true;
                    $title = implode(" ", array_slice( $title_arr, 0, $key-1 ) ).'...';
                    break;
                } 
            }
            $url            = get_site_url()."/katalog-gading/?&SingleProduct=".$post->Item_ID;
            $thumbnail      = $post->Item_Photo_URL;
            $normal_price   = $post->Item_Price;
            $discount_price = $post->Item_Sale_Price;
            ?>
            <div class="col-2" style="text-align:center;">
                <div class="booklist-thumbnail-container">
                    <span class="booklist-helper"></span><img class="booklist-thumbnail" src="<?php echo $thumbnail;?>">
                </div>
                <h6><a href="<?php echo $url;?>" title="<?php echo esc_attr( $post->Item_Name );?>"><?php echo esc_html( $title ); ?></a></h6>
                <p class="booklist-book-detail"><?php echo esc_html( $author ); ?></p>
                <?php if( $discount_price != "" ) :?> 
                    <p class="booklist-book-detail"><del><?php echo esc_html( $normal_price ); ?></del></p>
                    <p class="booklist-book-detail"><?php echo esc_html( $discount_price ); ?></p>
                <?php else :?>
                    <p class="booklist-book-detail"><?php echo esc_html( $normal_price ); ?></p>
                <?php endif;?>
            </div>
        <?php endforeach; ?>
        </div> <?php
    }
}
new Booklist;
?>