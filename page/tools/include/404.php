<?php include "header.php";?>
<style>
.page-main{
	background-color:#fff;
	width:100%;
	margin:0 auto;
	text-align:center;
}
@media screen and (max-width: 960px) {
	.page-main {width: 100%;}
}
</style>
<section class="page-main">
  <div class="admin-content">
    <div class="admin-content-body">
      <div class="am-g">
        <div class="am-u-sm-12">
          <h2 class="am-text-center am-text-xxxl am-margin-top-lg">404. Not Found</h2>
          <p class="am-text-center">没有找到你要的页面，点击<a href="<?=$this->permalink;?>">返回</a></p>
        <pre class="page-404">
          .----.
       _.'__    `.
   .--($)($$)---/#\
 .' @          /###\
 :         ,   #####
  `-..__.-' _.-\###/
        `;_:    `"'
      .'"""""`.
     /,  ya ,\\
    //  404!  \\
    `-._______.-'
    ___`. | .'___
   (______|______)
        </pre>
        </div>
      </div>
    </div>

  </div>
</section>