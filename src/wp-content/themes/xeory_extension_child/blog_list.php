<?php /*
Template Name: ブログ一覧
*/ ?>
<?php get_header(); ?>
	
<?php query_posts('post_type=blog&paged='.$paged); ?>
<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<div class="post">
			<p><?php the_time("Y年m月j日") ?></p>
			<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
			<?php if(has_post_thumbnail()) { the_post_thumbnail(); } ?>
			<?php global $more; $more = FALSE; ?>
				<?php //the_content('続きを読む'); ?>
				<?php the_excerpt(); //本文の抜粋の呼び出し?>
			<?php $more = TRUE; ?>
		</div>
		<hr>
	<?php endwhile; ?>
<?php else : ?>
	<div class="post">
		<h2>記事が見つかりません</h2>
		<p>記事が存在しないときのテキスト</p>
	</div>
<?php endif; ?>
<?php wp_reset_query(); ?>
<?php //get_sidebar(); ?>
