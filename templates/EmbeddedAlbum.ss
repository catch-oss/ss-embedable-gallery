<div class="embedded-album">
	<div class="embedded-album__feature">
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
		<% loop $Media.Limit(4,1) %>
			<a href="$Link" class="">
				<img src="$Image.FocusFill(600,400).Link" alt="$Title">
			</a>
		<% end_loop %>
	</div>
</div>
