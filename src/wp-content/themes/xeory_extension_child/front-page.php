<?php get_header(); ?>

<div id="main_visual">
	<img src="/wp-content/uploads/2018/05/top-new.png" alt="" class="pc_only">
	<img src="/wp-content/uploads/2018/05/top-new-sp.png" alt="" class="sp_only">
</div>

<div id="content">

<div id="main">
  <div class="main-inner">


<!--　「最近の投稿」は削除　-->

<!-- CLCとは <header>の部分は既存のものをコピーしているので変更したい場合はクラス名を変える必要があり -->
<div id="front-about-clc-contents" class="front-about-clc-cont">
  <div class="lead_logo">
    <img class="pc_only" src="/wp-content/uploads/2018/03/logo.png">
  </div>

  <div class="wrap top_what_clc">
    <h2 class="cont-title">Cafe Lounge Club（カフェラウンジクラブ）とは？</h2>
    <p>『Cafe Lounge Club（カフェラウンジクラブ）』とは、<br class="pc_only">カフェでよく商談をするビジネスマンのために、<br class="pc_only">提携カフェが割引価格で利用できるようになる会員制サービスです。<br>会員のお連れ様も割引になります。</p>
<br>
<div align="center"><p class="c_btn"><a href="/about" class="btn">詳しく見る</a></p></div>
<br>
<br>
  </div><!-- .wrap -->
</div>

<!-- クローズアップ -->
<div id="front-contents" class="front-main-cont">
<?php
  $icon = 'none';
  $bzb_ruby = '';
  $bzb_catch = '';
  $title = '';
  $bzb_contents_header_array = get_option('bzb_contents_header');
  if(is_array($bzb_contents_header_array)){
    extract($bzb_contents_header_array) ;
  }
?>
  <header class="category_title main_title front-cont-header">
    <div class="cont-icon"><i class="<?php echo $icon;?>"></i></div>
    <h2 class="cont-title"><?php echo $title;?></h2>
    <p class="cont-ruby"><?php echo $ruby;?></p>
    <div class="tri-border"><span></span></div>
  </header>

<?php
  $i = 1;
  $bzb_contents = get_option('bzb_contents');
  if(is_array($bzb_contents)){
    $left_right = "";
  foreach((array)$bzb_contents as $key => $value){
    $left_right = ($i % 2 == 1) ? 'right' : 'left';
    extract(make_info_4top($value));
?>
  <section id="front-contents-1" class="c_box c_box_<?php echo $left_right;?> c_box_num_<?php echo $i;?>">
    <div class="wrap">
      <div class="c_box_inner">
        <div class="c_title">
          <!--p class="c_number"><?php echo $i;?></p-->
          <h3><?php echo $title; ?></h3>
          <p class="c_english"><?php echo $bzb_ruby;?></p>
        </div>
        <div class="c_img_box" style="background-image:url(<?php echo $image;?>)"></div>
        <div class="c_text">
          <h4><?php echo nl2br($bzb_catch);?></h4>
          <p><?php echo $content;?></p>
<!--
          <?php if($button_url != ''){ ?>
          <p class="c_btn"><a href="<?php echo $button_url;?>" class="btn"><?php echo $button_text;?></a></p>
          <?php }else{ ?>
          <p class="c_btn"><a href="<?php echo $url;?>" class="btn">詳しく見る</a></p>
          <?php } ?>
 -->
        </div>
      </div>
    </div>
  </section>
<?php
  $i++;
    }
  }
?>


</div><!-- /front-contents -->


<!-- CLCをはじめる -->
<!-- CLCとは <header>の部分は既存のものをコピーしているので変更したい場合はクラス名を変える必要があり -->
<!--
<div id="front-about-clc-contents2" class="front-about-clc-cont">
  <header class="category_title main_title front-about-clc-header">
    <h2 class="cont-title">CLCをはじめる</h2>
  </header>
  <div class="wrap">
<br>
    <div align="center"><h2>お申込みはこちらから！</h2></div>
<br>
<div align="center"><p class="c_btn"><a href="/membership-join/membership-registration" class="btn">CLCをはじめる</a></p></div>
  </div>
-->
  <!-- .wrap -->
<!--
</div>
<br>
<br>
<br>
-->


<!-- 店舗のあるエリア・・・サービス記事（テーマで設定可能） -->
<?php
  $icon = 'none';
  $title = '';
  $bzb_ruby = '';
  $bzb_catch = '';
  $bzb_service_header_array = get_option('bzb_service_header');
  if(is_array($bzb_service_header_array)){
    extract($bzb_service_header_array) ;
  }

?>
<div id="front-service" class="front-main-cont">

  <header class="category_title main_title front-cont-header">
    <div class="cont-icon"><i class="<?php echo $icon;?>"></i></div>
    <h2 class="cont-title"><?php echo $title;?></h2>
    <p class="cont-ruby"><?php echo $ruby;?></p>
    <div class="tri-border"><span></span></div>
  </header>


  <div class="wrap">

<?php
  $i = 1;
  $bzb_service = get_option('bzb_service');
  if(isset($bzb_service)){
  foreach((array)$bzb_service as $key => $value){
    extract(make_info_4top($value));
?>
    <section class="front-service-1">
  　　　　<div class="new_front_card">
      <!-- アイキャッチ画像を差し込む -->
      <a href="<?php echo $url;?>">
        <div class="c_img_box" align="center"><img class="report_img" src="<?php echo $image;?>"></div>
      </a>
<style>/*
        <div class="c_title">
          <!-- h3→h4へ変更 -->
          <h4><?php echo $title;?></h4>
          <!-- 本文を抜粋表示したい -->
          <?php if($bzb_ruby){ ?>
          <p class="c_english"><?php echo $bzb_ruby;?></p>
          <?php }?>
        </div>
        <div class="c_text">
          <h4><?php echo nl2br($bzb_catch);?></h4>
          <?php if($service){ ?>
          <p><?php echo $service;?></p>
          <?php } ?>
          <?php if(isset($button_text) && $button_text !== '') { ?>
            <p class="c_btn"><a href="<?php echo $button_url;?>" class="btn"><?php echo $button_text;?></a></p>
          <?php }else{ ?>
            <p class="c_btn"><a href="<?php echo $url;?>" class="btn">詳しく見る</a></p>
          <?php } ?>
        </div>
*/</style>
       </div>
      </section>

  <?php
    }
  }
  ?>
    </div>
  </div>

</div><!-- /front-contents -->

<!-- もっとカフェに行こう -->
<div id="front-contents" class="front-main-cont">
<header class="category_title main_title front-cont-header">
  <h2 class="cont-title">もっとカフェに行こう。もっと人と話そう。</h2>
  <p class="cont-ruby"></p>
  <div class="tri-border"><span></span></div>
</header>

	<div id="go-cafe" class="wrap">
		<div class="go-cafe1">
      <img src="/wp-content/uploads/2018/05/01.png" alt="">
    </div>
		<div class="go-cafe2">
      <img src="/wp-content/uploads/2018/05/02.png" alt="">
    </div>
		<div class="go-cafe3">
      <img src="/wp-content/uploads/2018/05/03.png" alt="">
    </div>
		<div class="go-cafe4">
      <img src="/wp-content/uploads/2018/05/04.png" alt="">
    </div>
	</div>
		 

<!-- 申し込みの手順 -->
<!-- フルカスタマイズ -->
<div id="front-contents" class="front-main-cont">
<header class="category_title main_title front-cont-header">
  <h2 class="cont-title">お申込みの流れ</h2>
  <p class="cont-ruby"></p>
  <div class="tri-border"><span></span></div>
</header>

<div class="wrap">
  <div class="row">
  	<div class="col-sm-3">
      <div class="step-box">
        <img src="/wp-content/uploads/2018/03/step_01.png" class="stepNum"/>
        <h3><a href="/membership-join/membership-registration">会員登録フォーム</a>から会員登録を行います。</h3>
      </div>
      </div>
  	<div class="col-sm-3">
      <div class="step-box">
        <img src="/wp-content/uploads/2018/04/step_02.png" class="stepNum" />
        <h3>ガイダンスに従い、クレジットカード決済を行います</h3>
        <p></p>
      </div>
      </div>
  	<div class="col-sm-3">
      <div class="step-box">
        <img src="/wp-content/uploads/2018/03/step_03.png" class="stepNum" />
        <h3>登録内容を最終確認のうえ、登録ボタンを押すとメールが届きます。</h3>
        <p></p>
      </div>
      </div>
  	<div class="col-sm-3">
      <div class="step-box">
        <img src="/wp-content/uploads/2018/03/step_04.png" class="stepNum" />
        <h3>メールのURLをクリックし、会員ページへログイン後、会員証が確認できれば登録完了です。</h3>
        <p></p>
      </div>
    </div>
  </div>
<!--　2018.4.4 削除
	<div class="caution">
    先着<span>200名</span>91名限定の会員サービスです。<br>次回以降はサービス維持の為に値上げをする可能性があります。ご了承ください。
  </div>
-->
	</div>
</div><!-- 申し込みの手順 -->

<!-- 利用者の声 -->
<!-- フルカスタマイズ -->
<!--　一旦無しで！
<div id="front-contents" class="front-main-cont">
<header class="category_title main_title front-cont-header">
  <h2 class="cont-title">利用者の声</h2>
  <p class="cont-ruby"></p>
  <div class="tri-border"><span></span></div>
</header>
-->
<!-- いい感じにループさせたい -->
<!--　一旦無しで！
<div class="wrap">

<div class="front-loop-cont">
<?php
      $i = 1;
      if ( have_posts() ) :
        // wp_reset_query();

$args=array(
            'meta_query'=>
            array(
              array(  'key'=>'bzb_show_toppage_flag',
                         'compare' => 'NOT EXISTS'
              ),
              array(  'key'=>'bzb_show_toppage_flag',
                        'value'=>'none',
                        'compare'=>'!='
              ),
             'relation'=>'OR'
          ),
            'showposts'=>5,
            'post_type'=>'user_comment',
            'meta_key'=>'views',
            'orderby'=>'meta_value_num',
            'order'=>'DESC'
          );
        query_posts($args);
        // query_posts('showposts=5&post_type=user_comment&meta_key=views&orderby=meta_value_num&order=DESC');
        while ( have_posts() ) : the_post();

        $cf = get_post_meta($post->ID);
        $rank_class = 'popular_post_box rank-'.$i;
        // print_r($cf);
?>

  <article id="post-<?php echo the_ID(); ?>" <?php post_class($rank_class); ?>>
    <a href="<?php the_permalink(); ?>" class="wrap-a">

      <?php if( get_the_post_thumbnail() ) { ?>
      <div class="post-thumbnail">
        <?php the_post_thumbnail('loop_thumbnail'); ?>
      </div>
      <?php } else{ ?>
        <img src="<?php echo get_template_directory_uri(); ?>/lib/images/noimage.jpg" alt="noimage" width="800" height="533" />
      <?php } // get_the_post_thumbnail ?>
    <p class="p_category"><?php $cat = get_the_category(); $cat = $cat[0]; {
        echo $cat->cat_name;
      } ?></p>
    <h3><?php the_title(); ?></h3>
    <p class="p_rank">NO.<span><?php echo $i; ?></span></p>

    </a>
  </article>

<?php
        $i++;
        endwhile;
      endif;
?>
</div>
-->

<!-- </div> --><!-- /front-loop-cont -->

<!-- </div> --><!-- 利用者の声 -->


<!-- CLCをはじめる -->
<!-- CLCとは <header>の部分は既存のものをコピーしているので変更したい場合はクラス名を変える必要があり -->
<!--
<div id="front-about-clc-contents3" class="front-about-clc-cont">
  <header class="category_title main_title front-about-clc-header">
    <h2 class="cont-title">CLCをはじめる</h2>
  </header>
  <div class="wrap">
<br>
    <div align="center"><h2>お申込みはこちらから！</h2></div>
<br>
<div align="center"><p class="c_btn"><a href="/membership-join/membership-registration" class="btn">CLCをはじめる</a></p></div>
  </div>
-->
  <!-- .wrap -->
<!--
</div>
<br>
<br>
<br>
-->

<!--　会社概要は削除　-->

<!-- CLCをはじめる・・・お問い合わせ(テーマ設定で設定可能) -->
<!-- <div id="front-contact" class="front-main-cont"> -->
  <div id="front-contents" class="front-main-cont">
  <?php

  $icon = 0;
  $titile = '';
  $ruby = '';
  $bzb_contact_header_array = get_option('bzb_contact_header');
  if(is_array($bzb_contact_header_array)){
    extract($bzb_contact_header_array) ;
  }

  $bzb_contact_textarea = get_option('bzb_contact_textarea');
  ?>

  <header class="category_title main_title front-cont-header">
    <div class="cont-icon"><i class="<?php echo $icon;?>"></i></div>
    <h2 class="cont-title"><?php echo $title;?></h2>

        <?php echo $content = apply_filters( 'the_content', $bzb_contact_textarea, 10 ); ?>

    <p class="cont-ruby"><?php echo $ruby;?></p>
    <div class="tri-border"><span></span></div>
  </header>

  <div class="wrap">
    <div align="center">
      <div style="font-size: 1.5em;margin-bottom: 0.5em;">申し込みはこちらから！</div>
      <div class="c_btn"><a class="btn" href="/membership-join/membership-registration">CLCをはじめる</a></div>
    </div>

    <div class="card_brand">
      <div class="">
        <img src="/wp-content/uploads/2018/03/card_brand.png">
      </div>
      <span>※上記ロゴのついた全てのカードがご利用いただけます。</span>
    </div>

  </div>
</div><!-- front-contact -->


<!-- 人気のあるエリア -->
<!--
<div id="front-contents" class="front-main-cont">
<header class="category_title main_title front-cont-header">
  <h2 class="cont-title">人気のあるエリア</h2>
  <p class="cont-ruby"></p>
  <div class="tri-border"><span></span></div>
</header>

<div class="wrap">
  <div class="front-loop-cont">

<?php
      $i = 1;
      if ( have_posts() ) :
        // wp_reset_query();

        $args=array(
            'meta_query'=>
            array(
              array(  'key'=>'bzb_show_toppage_flag',
                         'compare' => 'NOT EXISTS'
              ),
              array(  'key'=>'bzb_show_toppage_flag',
                        'value'=>'none',
                        'compare'=>'!='
              ),
             'relation'=>'OR'
          ),
            'showposts'=>5,
            'meta_key'=>'views',
            'orderby'=>'meta_value_num',
            'order'=>'DESC'
          );
        query_posts($args);
        // query_posts('showposts=5&meta_key=views&orderby=meta_value_num&order=DESC');
        while ( have_posts() ) : the_post();

        $cf = get_post_meta($post->ID);
        $rank_class = 'popular_post_box rank-'.$i;
        // print_r($cf);
?>

  <article id="post-<?php echo the_ID(); ?>" <?php post_class($rank_class); ?>>
    <a href="<?php the_permalink(); ?>" class="wrap-a">

      <?php if( get_the_post_thumbnail() ) { ?>
      <div class="post-thumbnail">
        <?php the_post_thumbnail('loop_thumbnail'); ?>
      </div>
      <?php } else{ ?>
        <img src="<?php echo get_template_directory_uri(); ?>/lib/images/noimage.jpg" alt="noimage" width="800" height="533" />
      <?php } // get_the_post_thumbnail ?>
    <p class="p_category"><?php $cat = get_the_category(); $cat = $cat[0]; {
        echo $cat->cat_name;
      } ?></p>
    <h3><?php the_title(); ?></h3>
    <p class="p_rank">NO.<span><?php echo $i; ?></span></p>

    </a>
  </article>

<?php
        $i++;
        endwhile;
      endif;
?>
  </div>
-->
  <!-- /front-loop-cont -->
<!--
</div>
-->
<!-- /wrap -->
<!--
</div>
-->
<!-- popular_post_content -->


<!-- 公式LINEでお得情報をゲット！ -->
<!-- CLCとは <header>の部分は既存のものをコピーしているので変更したい場合はクラス名を変える必要があり -->
<div id="front-about-clc-contents4" class="front-about-clc-cont">
  <header class="category_title main_title front-about-clc-header">
    <h2 class="cont-title" style="color:#39ad37;">公式LINEの友達追加でお得情報をゲット！</h2>
  </header>
  <div class="wrap">
<div align="center"><p class="c_btn-line"><a href="https://line.me/R/ti/p/%40tyd1206t"><img src="/wp-content/uploads/2018/03/line.png"></a></p></div>
  </div><!-- .wrap -->
</div>

<!-- 飲食店様へ -->
<!-- CLCとは <header>の部分は既存のものをコピーしているので変更したい場合はクラス名を変える必要があり -->
<style>/*
<div id="offer_bg" class="front-about-clc-cont">
  <div id="offer" class="wrap">
   <h2>飲食店様へ<br>Cafe Lounge Club（カフェラウンジクラブ）では、会員へサービスを提供されたい飲食店様を大募集しています。</h2>
   <a href="/cafe-contact" class="btn">お問い合わせ希望はこちら</a>
  </div><!-- .wrap -->
</div>
*/
</style>

  </div><!-- /main-inner -->
</div><!-- /main -->

</div><!-- /content -->
<?php get_footer(); ?>
