<footer id="footer">
  <div class="footer-01">
    <div class="wrap">
<style>/*

      <div id="footer-brand-area" class="row">
        <div class="footer-logo gr6">
        <?php
        $logo_image = get_option('footer-logo');
          if( isset($logo_image) && $logo_image !== '' ){
        ?>
          <img src="<?php echo get_option('footer-logo');?>" alt="<?php echo get_bloginfo('name'); ?>" />
        <?php
          }else{
            echo get_bloginfo('name');
          }
        ?>
        </div>
        <div class="footer-address gr6">
          <?php echo get_option('footer-address');?>
        </div>
      </div><!-- /footer-brand-area -->
*/</style>

      <div id="footer-content-area">
        <div class="footer_logo">
          <img src="http://www.c-lounge.club/wp-content/uploads/2018/03/logo.png" alt="">
        </div>
        <div id="footer-list-area" class="footer-cont_menu">
          <div class="list_tit">メニュー</div>
          <div id="footer-cont-content" class="footer_menu">
            <ul>
				<li><a href="/about">CLCとは</a>
				</li><li><a href="/partner-list">提携店舗一覧</a></li>
				<li><a href="/faq">よくあるご質問</a></li>
				<li><a href="/contact">お問い合わせこちら</a></li>
				<li><a href="/cafe-contact">加盟店希望の飲食店様</a></li>
            </ul>
          </div>
          <div id="footer-cont-content" class="footer_menu">
            <ul>
				<li><a href="/privacy-policy">プライバシーポリシー</a></li>
				<li><a href="/law">特定商取引法の表記</a></li>
				<li><a href="/terms-of-service">利用規約</a></li>
				<li><a href="/sitemap">サイトマップ</a></li>
            </ul>
          </div>
        </div>
        <?php
         $facebook_page_url = 'https://www.facebook.com/cafe.lounge.club/';
         if(isset($facebook_page_url) && $facebook_page_url !== ''){
        ?>
        <!-- <div id="footer-facebook" class="gr12 text-right"> -->
        <div id="footer-facebook" class="footer_fb text-right">
            <div class="fb-page" data-href="<?php echo $facebook_page_url;?>" data-width="500" data-height="600" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true" data-show-posts="false"><div class="fb-xfbml-parse-ignore"><blockquote cite="<?php echo $facebook_page_url;?>"><a href="<?php echo $facebook_page_url;?>"><?php echo get_option('site_name');?></a></blockquote></div></div>
        </div>
        <?php } ?>
      </div>



    </div><!-- /wrap -->
  </div><!-- /footer-01 -->
  <div class="footer-02">
    <div class="wrap">
      <p class="footer-copy">
        © Copyright <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?>. All rights reserved.
      </p>
    </div><!-- /wrap -->
  </div><!-- /footer-02 -->
  <?php
  // }
  ?>
</footer>

<a href="#" class="pagetop"><span><i class="fa fa-angle-up"></i></span></a>
<?php wp_footer(); ?>

<script>
(function($){

$(function() {
    $("#header-fnav").hide();
  $("#header-fnav-area").hover(function(){
    $("#header-fnav").fadeIn('fast');
  }, function(){
    $("#header-fnav").fadeOut('fast');
  });
});


// グローバルナビ-サブメニュー
$(function(){
  $(".sub-menu").css('display', 'none');
  $("#gnav-ul li").hover(function(){
    $(this).children('ul').fadeIn('fast');
  }, function(){
    $(this).children('ul').fadeOut('fast');
  });
});

// トップページメインビジュアル
$(function(){
  h = $(window).height();
  hp = h * .3;
  $('#main_visual').css('height', h + 'px');
  $('#main_visual .wrap').css('padding-top', hp + 'px');
});

$(function(){
	if(window.innerWidth < 768) {
  h = $(window).height();
  hp = h * .2;
  $('#main_visual').css('height', h + 'px');
  $('#main_visual .wrap').css('padding-top', hp + 'px');
	}
});

// sp-nav
$(function(){
  var header_h = $('#header').height();
  $('#gnav-sp').hide();

    $(window).resize(function(){
      var w = $(window).width();
      var x = 991;
      if (w >= x) {
          $('#gnav-sp').hide();
      }
  });

  $('#gnav-sp').css('top', header_h);
  $('#header-nav-btn a').click(function(){
    $('#gnav-sp').slideToggle();
    $('body').append('<p class="dummy"></p>');
  });
  $('body').on('click touchend', '.dummy', function() {
    $('#gnav-sp').slideUp();
    $('p.dummy').remove();
    return false;
  });
});

})(jQuery);

</script>


</body>
</html>
