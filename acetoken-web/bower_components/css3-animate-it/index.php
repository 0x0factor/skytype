<!DOCTYPE html>
<html lang="en-US" class="no-js">
	<head>
		<link rel="stylesheet" href="css/animations.css" type="text/css">

		<!-- STUFF JUST FOR THIS TEMPLATE -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="example-assets/style.css" type="text/css">
	</head>
	<body>
		<header class='mainHeader'>
			<div class='container'>
				<div class='animatedParent' data-sequence='500' >
					<h1 class='animated growIn slower'  data-id='1'>CSS3 Animate It</h1>
					<h2 class='animated bounceInRight slower'  data-id='2'>Because static content is boring!</h2>
					<div class='buttons-inline animated bounceInLeft slower'   data-id='3'><a class="btn btn-primary" href="examples.php">See Animation List</a></div>
					<div class='buttons-inline animated bounceInRight slower' data-id='4'><a class='btn btn-default' href='https://github.com/Jack-McCourt/css3-animate-it/archive/master.zip'>Download!</a></div>
					<div class='animated bounceInUp findMe' data-id='5'>Find me at <a  target="_blank" href='http://jackonthe.net'>jackonthe.net</a></div>
				</div>
			</div>
		</header>
		<div class='container'>
			<div class='animatedParent'>
				<h2 class='animated growIn slowest'>Quick Start</h2>
			</div>
			<div class='animatedParent col-md-6' data-sequence='1000' >
				<p class='animated bounceInLeft' data-id='1'>
					Include <a href='js/css3-animated.js'>css3-animated.js</a> at the end of your file and <a href='css/animations.css'>animations.css</a> in the head.
				</p>
				<p class='animated bounceInLeft' data-id='2'>
					Once you have done that you can just define <b>"animatedParent"</b> as the parent class which is what will trigger the child <b>class "animated"</b> to animate whichever animation is defined, here we are using <b>"bounceInDown"</b>. 
				</p>
				<p class='animated bounceInLeft' data-id='3'>
					<a href='examples.php'>Click here</a> to see an example of all the animations.
				</p>
			</div>
			<div class='animatedParent col-md-6'>
<pre class='animated bounceInRight slow'>
<?php echo htmlspecialchars("<div class='"); ?><span class='highlighter'><?php echo htmlspecialchars("animatedParent") ?></span><?php echo htmlspecialchars("'>") ?>

	<?php echo htmlspecialchars("<h2 class='"); ?><span class='highlighter'><?php echo htmlspecialchars("animated bounceInDown"); ?></span><?php echo htmlspecialchars("'>It Works!</h2>"); ?>

<?php echo htmlspecialchars("</div>"); ?>
</pre>
			</div>
		</div>

		<div class='greyBackground'>
			<div class='container' >
				<div class='animatedParent' >
					<h2 class='animated growIn slow'>Extra Functions</h2>
				</div>

				<!-- Sequencing -->
				<div class='animatedParent row article'>
				<h3 class='animated bounceInDown'>Sequencing</h3>
					<div class='col-md-4'>
						
						<p class='animated bounceInLeft slow'>
							If you want to have a set of animations start one after the other then you can set a sequence time in ms using "data-sequence" then define the order with "data-id".
						</p>
					</div>
					<div class='animatedParent col-md-8'>
<pre class='animated bounceInRight slow'>
<?php echo htmlspecialchars("<div class='animatedParent' "); ?><span class='highlighter'><?php echo htmlspecialchars("data-sequence='500'") ?></span><?php echo htmlspecialchars(">") ?>

	<?php echo htmlspecialchars("<h2 class='animated bounceInDown' "); ?><span class='highlighter'><?php echo htmlspecialchars("data-id='1'"); ?></span><?php echo htmlspecialchars(">It Works!</h2>"); ?>

	<?php echo htmlspecialchars("<h2 class='animated bounceInDown' "); ?><span class='highlighter'><?php echo htmlspecialchars("data-id='2'"); ?></span><?php echo htmlspecialchars(">This animation will start 500ms after</h2>"); ?>

	<?php echo htmlspecialchars("<h2 class='animated bounceInDown' "); ?><span class='highlighter'><?php echo htmlspecialchars("data-id='3'"); ?></span><?php echo htmlspecialchars(">This animation will start 500ms after</h2>"); ?>

<?php echo htmlspecialchars("</div>"); ?>
</pre>
					</div>
				</div>

				<div class='animatedParent'>
					<hr class='animated fadeIn slow' />
				</div>

				<!-- Offset -->
				<div class='animatedParent row article'>
					<h3 class='animated bounceInDown'>Offset</h3>
					<div class='col-md-6'>
						<p class='animated bounceInLeft slow'>
							This will make the make the animation either start before or after it has entered the viewport by the specified ammount. So if you wanted to only start the animation after the user has scrolled 300px past it then setting an offset of -300px would achieve this.
						</p>
					</div>
					<div class='animatedParent col-md-6'>
<pre class='animated bounceInRight slow'>
<?php echo htmlspecialchars("<div class='animatedParent'"); ?><span class='highlighter'><?php echo htmlspecialchars("data-appear-top-offset='-300'") ?></span><?php echo htmlspecialchars(">") ?>

	<?php echo htmlspecialchars("<h2 class='animated bounceInDown'>It Works!</h2>"); ?>

<?php echo htmlspecialchars("</div>"); ?>
</pre>
					</div>
				</div>
			
		
	
				<div class='animatedParent'>
					<hr class='animated fadeIn slow' />
				</div>

				<!-- Animate Once -->
				<div class='animatedParent row article'>
					<h3 class='animated bounceInDown'>Animate Once</h3>
					<div class='col-md-6'>
						<p class='animated bounceInLeft slow'>
							Adding this will make sure the item only animates once and will not reset when it leaves the viewport.
						</p>
					</div>
					<div class='animatedParent col-md-6'>
<pre class='animated bounceInRight slow'>
<?php echo htmlspecialchars("<div class='animatedParent "); ?><span class='highlighter'><?php echo htmlspecialchars("animateOnce") ?></span><?php echo htmlspecialchars("'>") ?>

	<?php echo htmlspecialchars("<h2 class='animated bounceInDown'>It Works!</h2>"); ?>

<?php echo htmlspecialchars("</div>"); ?>
</pre>
					</div>
				</div>

				<div class='animatedParent'>
					<hr class='animated fadeIn slow' />
				</div>

				<!-- Animate Once -->
				<div class='animatedParent row article'>
					<h3 class='animated bounceInDown'>Animation Speed</h3>
					<div class='col-md-6'>
						<p class='animated bounceInLeft slow'>
							Currently you can define 4 speeds, the default which requires nothing then slow, slower and slowest.
						</p>
					</div>
					<div class='animatedParent col-md-6'>
<pre class='animated bounceInRight slow'>
<?php echo htmlspecialchars("<div class='animatedParent'"); ?><span class='highlighter'><?php echo htmlspecialchars("") ?></span><?php echo htmlspecialchars(">") ?>

	<?php echo htmlspecialchars("<h2 class='animated bounceInDown "); ?><span class='highlighter'><?php echo htmlspecialchars("slowest"); ?></span><?php echo htmlspecialchars("'>It Works!</h2>"); ?>

<?php echo htmlspecialchars("</div>"); ?>
</pre>
					</div>
				</div>


				<div class='animatedParent'>
					<hr class='animated fadeIn slow' />
				</div>

				

				<!-- Delay -->
				<div class='animatedParent row article'>
					<h3 class='animated bounceInDown'>Delay (New)</h3>
					<div class='col-md-6'>
						<p class='animated bounceInLeft slow'>
							You can now add individual delays to your animations if you don't want to do it with sequencing delays, please refer to the animations css file to see all the delays available.
						</p>
					</div>
					<div class='animatedParent col-md-6'>
	<pre class='animated bounceInRight slow'>
<?php echo htmlspecialchars("<div class='animatedParent'"); ?><span class='highlighter'><?php echo htmlspecialchars("") ?></span><?php echo htmlspecialchars(">") ?>

	<?php echo htmlspecialchars("<h2 class='animated bounceInDown "); ?><span class='highlighter'><?php echo htmlspecialchars("delay-250"); ?></span><?php echo htmlspecialchars("'>It Works!</h2>"); ?>

<?php echo htmlspecialchars("</div>"); ?>
	</pre>
		</div>
			
				</div>

				<div class='animatedParent'>
					<hr class='animated fadeIn slow' />
				</div>

				<!-- On Click  -->
				<div class='animatedParent row article'>
					<h3 class='animated bounceInDown'>On Click Toggle</h3>
					<div class='col-md-6'>
						<p class='animated bounceInLeft slow'>
							You can now call the animations with an on click event. You can can define an out animation for transitioning out with this. <input type='button' class='btn btn-primary animatedClick' data-target='clickExample' value='Click me!'>
						</p>
						<p class='animated bounceInLeft slow'>
							Please note that the target will only animate on view if you wrap it in an animatedParent class. If it is not in this then the animation will only trigger on click.
						</p>
					</div>
					<div class='animatedParent col-md-6'>
<pre class='animated bounceInRight fadeOutDown slow clickExample'>
<?php echo htmlspecialchars("<input type='button' class='"); ?><span class='highlighter'><?php echo htmlspecialchars("animatedClick") ?></span><?php echo htmlspecialchars("'"); ?> <span class='highlighter'>data-target='clickExample'</span><?php echo htmlspecialchars(">"); ?>

<?php echo htmlspecialchars("<h2 class='animated bounceInDown "); ?><span class='highlighter'><?php echo htmlspecialchars("clickExample"); ?></span> <span class='highlighter'><?php echo htmlspecialchars("fadeOutDown"); ?></span><?php echo htmlspecialchars("'>It Works!</h2>"); ?>


</pre>
					</div>
				</div>

				<div class='animatedParent'>
					<hr class='animated fadeIn slow' />
				</div>

				<!-- On Click Sequencing -->
				<div class='animatedParent row article'>
					<h3 class='animated bounceInDown'>On Click With Sequencing</h3>
					<div class='col-md-5'>
						<p class='animated bounceInLeft slow'>
							You can now call the animations with an on click event. You can can define an out animation for transitioning out with this. 
						</p>
					</div>
					<div class='animatedParent col-md-7'>
<pre class='animated bounceInRight slow'>
<?php echo htmlspecialchars("<input type='button' class='animatedClick' data-target='clickExample'"); ?> <span class='highlighter'>data-sequence='500'</span><?php echo htmlspecialchars(">"); ?>

<?php echo htmlspecialchars("<h2 class='animated bounceInDown clickExample fadeOutDown' "); ?><span class='highlighter'>data-id='1'</span><?php echo htmlspecialchars(">It Works!</h2>"); ?>

<?php echo htmlspecialchars("<h2 class='animated bounceInDown clickExample fadeOutDown' "); ?><span class='highlighter'>data-id='2'</span><?php echo htmlspecialchars(">It Works!</h2>"); ?>

</pre>
					</div>
				</div>


				<div class='animatedParent'>
					<hr class='animated fadeIn slow' />
				</div>


				<!-- IE Fix -->
				<div class='animatedParent row article'>
					<h3 class='animated bounceInDown'>IE Fix</h3>
					<div class='col-md-5'>
						<p class='animated bounceInLeft slow'>
							This is just a fix that will fix the elements not appearing on IE9 or less, please not that the animations will not work on IE9 or less. 
							<br /><br />
							Place this below the animations.css link in the head of your file.
						</p>
					</div>
					<div class='animatedParent col-md-7'>
<pre class='animated bounceInRight slow'>
<?php echo htmlspecialchars("<!--[if lte IE 9]>
      <link href='/PATH/TO/FOLDER/css/animations-ie-fix.css' rel='stylesheet'>
<![endif]-->"); ?>

</pre>
					</div>
				</div>
			</div>
		</div>
			</div>
		
		</div>


		<div class='animatedParent' data-sequence='1000'>
			<footer class='animated growIn' data-id='1'>
				<div class='container'>
					<div class='row'>
						<p class="col-md-10">&copy;2014 All rights reserved. Designed by Jack McCourt at <b><a href='http://jackonthe.net' class='animated bounceInRight' data-id='2'>jackonthe.net</a></b></p>
					</div>
				</div>
			</footer>
		</div>
	</body>
</html>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src='js/css3-animate-it.js'></script>
