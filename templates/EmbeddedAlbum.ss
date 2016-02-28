<!--  these class names are not so awesome -->
<div class="embedded-album">
	<div class="embedded-album__feature icon-search icon-magnify">
		<% with $Media.First %>
			<a href="$Link" class="">
				<img src="$Image.FocusFill(600,400).Link" alt="$Title">
			</a>
		<% end_with %>
		<div class="embedded-album__feature__copy">
			$Content
			<% if $Credit %>
				<p class="credit">Photo: $Credit</p>
			<% end_if %>
		</div>
	</div>
	<div class="embedded-album__extra">
		<% loop $Media.Limit(1,4) %>
			<a href="$Link" class="">
				<img src="$Image.FocusFill(600,400).Link" alt="$Title">
			</a>
		<% end_loop %>
	</div>
</div>
