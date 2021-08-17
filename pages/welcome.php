<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Welcome To sho.rt API</title>
</head>
<style type="text/css">
	html, body {
		padding: 0;
		margin: 0;
		width: 100%;
		height: 100%;
		outline: none;
		font-family: 'Fira Sans';
		font-weight: 100;
		display: flex;
		align-items: center;
		justify-content: center;
		text-align: center;
		background-color: #4F3074;
		color: #fff;
	}
	h4 {
		font-weight: 400;
		text-transform: uppercase;
		color: #999;
		padding-bottom: 1rem;
	}
	code {
		font-family: 'Fira Code', sans-serif;
		font-size: 14px;
	}
	.elm__2d {
		padding-top: 1rem;
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		grid-gap: 1rem;
	}
	.elm__2d .elm__1d:nth-child(1) {
		grid-column: 1 / span 2;
	}
	.elm__2d .elm__1d {
		text-align: left;
		background-color: #444;
		padding: 10px;
		border-radius: 4px;
	}
	.elm__2d .elm__1d h4 {
		text-align: center;
	}
	.color__yellow { color: yellow; }
	.color__green { color: lime; }
</style>
<body>

	<div class="app">
		<h3>Welcome To Sho.rt API</h3>
		<p>You can shorten and share your link easily with us. Please refer to the usage manual on how to use the API.</p>


		<div class="elm__2d">
			<div class="elm__1d">
				<h4>Shorten Links</h4>

				<pre><code>$ <span class="color__yellow">POST</span>           -> /shorten</code></pre>
				<pre><code>$ <span class="color__yellow">Content-Type</span>   -> application/x-www-form-url-encoded</code></pre>
				<pre><code>$ <span class="color__yellow">Body</span>           -> link={your_long_link}</code></pre>
			</div>
			
			<div class="elm__1d">
				<h4>Response</h4>

				<pre><code>$ <span class="color__yellow">Json Object</span></code></pre>
				<pre><code><span class="color__green">{ status, link_id, short_link }</span></code></pre>

			</div>

			<div class="elm__1d">
				<h4>Request Links</h4>

				<pre><code>$ <span class="color__yellow">GET</span> -> /{link_id}</code></pre>
				<pre><code>$ <span class="color__yellow">302</span> -> Redirect to the original link</code></pre>
			</div>
			
		</div>

	</div>

</body>
</html>