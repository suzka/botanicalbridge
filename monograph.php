<?php
/*
Template Name: Monograph
*/

?>
<?php get_header(); ?>

<div id="main" class="full-width">

	<div class="wrapper wrapper-main">
	
		<div id="content">
		
			<div class="wrapper-content-inside">
			
				<div class="wpzoom-breadcrumbs">
					<p class="crumbs"><?php wpzoom_breadcrumbs(); ?></p>
				</div><!-- end .wpzoom-breadcrumbs -->

				<?php while (have_posts()) : the_post(); ?>
				
				<div class="post-meta-single">
					<h1 class="title-post-single"><?php the_title(); ?></h1>
					<?php edit_post_link( __('Edit page', 'wpzoom'), '<p class="post-meta">', '</p>'); ?>
				</div><!-- end .post-meta-single -->
	
				<div class="divider">&nbsp;</div>
	
				<div class="post-single">
				
					<?php the_content(); ?>
					
					<div class="cleaner">&nbsp;</div>
					
					<?php wp_link_pages(array('before' => '<div class="navigation"><p><strong>'.__('Pages', 'wpzoom').':</strong> ', 'after' => '</p></div>', 'next_or_number' => 'number')); ?>
					
				</div><!-- .post-single -->
				<div class="activities"> <!-- .post-activities -->
					<?php
					$servername = "127.0.0.1:3306";
                    $username = "root";
					$password = "KR8chard";
					$dbname = "drduke";
					//$genus = 'Aloe';
					//$species = 'vera';

					// Create connection
					$conn = new mysqli($servername, $username, $password, $dbname);

					// Check connection
					if ($conn->connect_error) {
    					die("Connection failed: " . $conn->connect_error);
					} 
					// echo good db connection
					//echo "<p>Connected to " . $dbname . " database</p>";

					// Run a query of phytochemicals
					// Sample query
					//$sql = "SELECT ACTIVITY FROM ethnobot WHERE GENUS = 'Aloe' AND SPECIES = 'vera' ";
					//$sql = "SELECT ACTIVITY FROM ethnobot WHERE GENUS = '" . $_GET['genus'] . "' AND SPECIES = '" . $_GET['species'] ."' ";
					$sql = "SELECT farmacy_new.CHEM FROM fnftax INNER JOIN farmacy_new ON fnftax.FNFNUM = farmacy_new.FNFNUM	WHERE fnftax.GENUS = 'Aloe' AND fnftax.SPECIES = 'vera'	ORDER BY farmacy_new.AMT_HI DESC LIMIT 20";
					$result = mysqli_query($conn,$sql);
					echo "<h2 id='drduke'>Top Phytochemicals found in " . $_GET['genus'] . " " . $_GET['species']. "</h2>";

					echo "<p></br>Source: U.S. Department of Agriculture, Agricultural Research Service. 1992-2016. Dr. Duke's Phytochemical and Ethnobotanical Databases. </br>Home Page: <a href='http://phytochem.nal.usda.gov/'>http://phytochem.nal.usda.gov/</a> 
   						 	</p>";

					if ($result->num_rows > 0) {
   						 // output data of each row
						
						echo "<div class='chem-list'>";
						echo "<p><ul class='container'>";
						
    					while($row = mysqli_fetch_assoc($result)) {
        					echo  "<li class='chem'> " . $row["CHEM"]. "</li>";
   						 }

   						 echo "</ul></p></div>";
   						 
					} else {
    					echo "<p>No phytochemicals for " .  $_GET['genus'] . " <em>" . $_GET['species'] . "</em> found in Dr. Duke's Phytochemical & Ethnobotanical Databases!</p>";
					}

					$conn->close();

					?>

				</div> <!--  end .post-activities -->
				
				<div id="post-pubmed">

					<?php
					include 'MyQuery.php';
					$query  = new MyQuery();
					$result = $query->get($_GET['genus'] . " " .$_GET['species']);
					?>
				</div> <!-- end .post-pubmed -->

				<?php if (option::get('page_share') == 'on') { ?>
				
				<div class="post-share">
					<span class="share_btn"><a href="http://twitter.com/share" data-url="<?php the_permalink() ?>" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></span>
					<span class="share_btn"><iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=120&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px;" allowTransparency="true"></iframe></span>
					<span class="title"><?php _e('Share this page','wpzoom'); ?></span>
				</div><!-- end .post-share -->
				
				<?php } ?>
	
				<?php if (option::get('page_comments') == 'on') { ?>
	
				<?php comments_template(); ?>
		
				<?php } ?>
				
				<?php endwhile; ?>

				<div class="cleaner">&nbsp;</div>
			
			</div>

			<div class="cleaner">&nbsp;</div>
		
		</div><!-- #content -->
		
		<div class="cleaner">&nbsp;</div>

	</div><!-- .wrapper .wrapper-main -->

</div><!-- end #main -->

<?php get_footer(); ?>