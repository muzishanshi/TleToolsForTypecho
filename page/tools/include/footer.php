	<footer>
		<style>
		#topBtn {
		  display: none;
		  position: fixed;
		  bottom: 20px;
		  right: 30px;
		  z-index: 99;
		  border: none;
		  outline: none;
		  background-color: #dddddd;
		  color: white;
		  cursor: pointer;
		  padding: 15px;
		  border-radius: 10px;
		}
		#topBtn:hover {
		  background-color: #555;
		}
		</style>
		<button onclick="topFunction()" id="topBtn" title="回顶部">返回顶部</button>
		<div><?php TleTools_Plugin::printLinks();?></div>
		<p style="text-align:center">
			<small>
				<br>&copy; 2018 <a href="<?=$this->options ->siteUrl();?>" target="_blank"><?php $this->options->title();?></a> and Powered by <a href="http://www.tongleer.com" target="_blank">同乐儿</a>. All rights reserved.
			</small>
		</p>
		<script>
		/*当网页向下滑动 20px 出现"返回顶部" 按钮*/
		window.onscroll = function() {scrollFunction()};
		function scrollFunction() {console.log(121);
			if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
				document.getElementById("topBtn").style.display = "block";
			} else {
				document.getElementById("topBtn").style.display = "none";
			}
		}
		/*点击按钮，返回顶部*/
		function topFunction() {
			document.body.scrollTop = 0;
			document.documentElement.scrollTop = 0;
		}
		</script>
		<script src="//cdnjs.loli.net/ajax/libs/mdui/0.4.2/js/mdui.min.js"></script>
	</footer>
</body>
</html>