<?php get_header(); ?>

<div id="content">

<div class="breadcrumb-area index_hoge">
  <div class="wrap">
    <?php bzb_breadcrumb(); ?>
  </div>
</div>

<div class="wrap">

<!-- カスタマイズ -->
<!-- 利用店舗一覧ページのトップに「条件で探す」リンクを設置 -->
<br>
◎利用可能店舗を検索
<br><br>
<div class="search-area">
   <p class="c_btn"><a href="#categories-2">条件で探す</a></p>
</div>
<br><br>

◎利用可能店舗一覧
  <div id="main" <?php bzb_layout_main(); ?> role="main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Blog">
    <div class="main-inner">
    
<!--    
    <?php if( !is_front_page()){?>
          <h1 class="post-title" ><?php bzb_title(); ?></h1>
    <?php } ?>
-->    
    <div class="post-loop-wrap">
    <?php
      
			if ( have_posts() ) :

				while ( have_posts() ) : the_post();

        $cf = get_post_meta($post->ID); ?>
    <article id="post-<?php echo the_ID(); ?>" <?php post_class(); ?> itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
      
      
      

      <header class="post-header">
        <div class="cat-name">
          <span>
            <?php
              $category = get_the_category(); 
              echo $category[0]->cat_name;
            ?>
          </span>
        </div>
        <h2 class="post-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
      </header>
<!--      
      <div class="post-meta-area">
        <ul class="post-meta list-inline">
          <li class="date" itemprop="datePublished" datetime="<?php the_time('c');?>"><i class="fa fa-clock-o"></i> <?php the_time('Y.m.d');?></li>
        </ul>
        <ul class="post-meta-comment">
          <li class="author">
            by <?php the_author(); ?>
          </li>
          <li class="comments">
            <i class="fa fa-comments"></i> <span class="count"><?php comments_number('0', '1', '%'); ?></span>
          </li>
        </ul>
      </div>
-->      
      <?php if( get_the_post_thumbnail() ) { ?>
      <div class="post-thumbnail">
        <a href="<?php the_permalink(); ?>" rel="nofollow"><?php the_post_thumbnail('big_thumbnail'); ?></a>
      </div>
      <?php } ?>

      <section class="post-content" itemprop="text">
        <?php the_excerpt(); ?>
      </section>
      
      <footer class="post-footer">
        <a class="morelink" href="<?php the_permalink(); ?>" rel="nofollow">店舗詳細情報を見る ≫</a>
      </footer>

    </article>

    <?php
    
				endwhile;

			else :
		?>

    <article id="post-404"class="cotent-none post" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
      <section class="post-content" itemprop="text">
        <?php echo get_template_part('content', 'none'); ?>
      </section>
    </article>
    <?php
			endif;
		?>

<?php if (function_exists("pagination")) {
    pagination($wp_query->max_num_pages);
} ?>

    </div><!-- /post-loop-wrap -->



    </div><!-- /main-inner -->
  </div><!-- /main -->
  
<?php get_sidebar(); ?>

</div><!-- /wrap -->
  
</div><!-- /content -->

<?php get_footer(); ?>


