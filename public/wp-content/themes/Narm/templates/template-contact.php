<?php
/**
 * The main template file for display contact page.
 *
 * @package WordPress
 * @subpackage Narm
*/


/**
*	if not submit form
**/
if(!isset($_POST['your_name']))
{
get_header(); 

$page_description = get_post_meta($current_page_id, 'page_description', true);
$page_sidebar = get_post_meta($current_page_id, 'page_sidebar', true);

if(empty($page_sidebar))
{
	$page_sidebar = 'Contact Sidebar';
}

$caption_class = 'page_caption';

?>

		<!-- Begin content -->
		<div id="content_wrapper">
		
			<br class="clear"/>
		
			<div class="<?php echo $caption_class?>">
				<div class="caption_inner">
					<h1 class="cufon"><?php the_title(); ?></h1>
					
					<?php
						if(!empty($page_description))
						{
					?>
							<p class="cufon"><?php echo $page_description?></p>
					<?php
						}
					?>
				</div>
			</div>
			
			<div class="inner">
				
				<!-- Begin main content -->
				<div class="inner_wrapper">
				
					<div class="sidebar_content">
						<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>		

							<?php the_content(); ?><br/><br/>

						<?php endwhile; ?>
						
						<form id="contact_form" method="post" action="<?php echo curPageURL(); ?>">
						    <p>
						    	<label for="your_name">Name</label><br/>
						    	<input id="your_name" name="your_name" type="text" style="width:94%"/>
						    </p>
						    <p style="margin-top:20px">
						    	<label for="email">Email</label><br/>
						    	<input id="email" name="email" type="text" style="width:94%"/>
						    </p>
						    <p style="margin-top:20px">
						    	<label for="message">Message</label><br/>
						    	<textarea id="message" name="message" rows="7" cols="10" style="width:94%"></textarea>
						    </p>
						    <p style="margin-top:20px">
								<a class="long_button" href="javascript:;" onclick="$('#contact_form').submit();">
									<span>Send Message</span>
								</a>
							</p>
						</form>
						<div id="reponse_msg"></div>
						<br/><br/>
						
					</div>
					
					<div class="sidebar_wrapper">
						<div class="sidebar">
							
							<div class="content">
							
								<ul class="sidebar_widget">
								<?php dynamic_sidebar($page_sidebar); ?>
								</ul>
								
							</div>
						
						</div>
						<br class="clear"/>
					
						<div class="sidebar_bottom"></div>
					</div>
				
				</div>
				<!-- End main content -->
				
				<br class="clear"/>
			</div>
			
			<div class="bottom"></div>
			
		</div>
		<!-- End content -->
				

<?php get_footer(); ?>
				
				
<?php
}

//if submit form
else
{

	/*
	|--------------------------------------------------------------------------
	| Mailer module
	|--------------------------------------------------------------------------
	|
	| These module are used when sending email from contact form
	|
	*/
	
	//Get your email address
	$contact_email = get_option('nm_contact_email');
	
	//Enter your email address, email from contact form will send to this addresss. Please enter inside quotes ('myemail@email.com')
	define('DEST_EMAIL', $contact_email);
	
	//Change email subject to something more meaningful
	define('SUBJECT_EMAIL', 'Email from contact form');
	
	//Thankyou message when message sent
	define('THANKYOU_MESSAGE', 'Thank you! We will get back to you as soon as possible');
	
	//Error message when message can't send
	define('ERROR_MESSAGE', 'Oops! something went wrong, please try to submit later.');
	
	
	/*
	|
	| Begin sending mail
	|
	*/
	
	$from_name = $_POST['your_name'];
	$from_email = $_POST['email'];
	
	$message = 'Name: '.$from_name.PHP_EOL;
	$message.= 'Email: '.$from_email.PHP_EOL.PHP_EOL;
	$message.= 'Message: '.PHP_EOL.$_POST['message'];
	    
	
	if(!empty($from_name) && !empty($from_email) && !empty($message))
	{
		mail(DEST_EMAIL, SUBJECT_EMAIL, $message);
	
		echo THANKYOU_MESSAGE;
		echo '</p>';
		
		exit;
	}
	else
	{
		echo ERROR_MESSAGE;
		
		exit;
	}
	
	/*
	|
	| End sending mail
	|
	*/
}

?>