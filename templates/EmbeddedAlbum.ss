<!--  these class names are not so awesome -->
<div class="post-news-thumbnail-container">
	<div class="post-news-thumbnail icon-search icon-magnify">
		<% with $Media.First %>
			<a href="$Link" class="">
				<img src="$Image.FocusFill(600,400).Link" alt="$Title">
			</a>
		<% end_with %>
		<div class="post-news-thumbnail-copy">
			$Content
			<% if $Credit %>
				<p class="getty-copyright">Photo: $Credit</p>
			<% end_if %>
		</div>
	</div>
	<div class="post-news-thumbnail-extra">
		<% loop $Media.Limit(1,4) %>
			<a href="$Link" class="">
				<img src="$Image.FocusFill(600,400).Link" alt="$Title">
			</a>
		<% end_loop %>
	</div>
</div>
