<?php
/**
 * @package signup
 * @version 1.0
 */
/*
Plugin Name: 123signup
Plugin URI: https://signup.ua
Description: 123signup
Armstrong: My Plugin.
Author: Maxim Rudniy
Version: 1.0
Author URI: http://maxim.webro.com.ua
*/
define("FORM_PAGE", "/123_signup_form_page/");
define("LOGIN_PAGE", "/123_login_form_page/"); // сторінка виведення форми авторизації
define("SUCCESS_URL", "/123_signup_success_page/"); //сторінка обробки даних після авторизації на 123signup
define("ERROR_URL", "/123_signup_error_page/"); // сторінка виведення помилки про авторизацію і форму входу
define("PREFIX", "123signup"); // префікс для таблиці бази даних
define("USERS_TABLE", "social_users");
define("SALT", '2343sdfxcvSDFss'); // salt for cookies sign up users
define("ORG_ID", 'wtf'); // org id


function show_signup_page(){
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
    <HTML>
    <HEAD>
        <TITLE>Testing of DAL # 1</TITLE>

        <LINK REV="made" HREF="mailto:">
        <META name="keywords" content="">
        <META name="description" content="">

    </HEAD>
    <BODY>
    <h3>Level 1 Profile Authentication</h3>
    <p>If your login will successful, you will be redirected to home page.
        <br>If your login will be unsuccessful, you will be redirected to failure url.

    <?php show_just_form();?>

    </body>
    </html>

<?php
    die();
}

function show_just_form(){
    ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js"></script>
    <FORM method="POST" id="form" action="https://www.123signup.com/servlet/DAP">
        <table border = '1'>
            <tr>
                <td>E-mail Address:</td>
                <td><INPUT NAME="email" TYPE="Text" id="email" value="" required></INPUT></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><INPUT NAME="Password" TYPE="Password" value="" required></INPUT></td>
            </tr>
            <tr>
                <td colspan='2'><INPUT NAME="Submit" id="submit_btn" TYPE="Submit" value='Log in' class=""></INPUT></td>
            </tr>
        </table>
        <br><br>

        <INPUT NAME="DAL" TYPE="Hidden" value='1'></INPUT>
        <input type="hidden" name="org_id" value="<?php echo ORG_ID;?>"/>
        <input type="hidden" name="SuccessURL" value="<?php echo 'http://'.$_SERVER['SERVER_NAME'].SUCCESS_URL;?>">
        <input type="hidden" name="FailureURL" value="<?php echo 'http://'.$_SERVER['SERVER_NAME'].ERROR_URL;?>">
        <input type="hidden" name="MailingList" value="1">

    </FORM>
    <script>
        $('#form').submit(function(){
            $.cookie('email123', $('#form #email').val(), { expires: 1, path: '/' });
        });
    </script>
    <?php
}


function create_user_and_login(){
    global $wpdb;
    $email = isset( $_COOKIE['email123'] ) ? $_COOKIE['email123'] : NULL;
    setcookie( 'email123', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN ); //delete cookie
    if ( ! $email )
        return;
    $email = sanitize_email($email);

    try {
        setcookie( 'user123', md5($email).SALT, time() + 3600*24*7, COOKIEPATH, COOKIE_DOMAIN ); //delete cookie
        wp_redirect('/private');
        exit;

    }
    catch(Exception $e) {
        echo 'Caught exception: ', $e->getMessage() , "\n";
    }

    wp_redirect( home_url() );

    die();
}

function signup_run($content)
{
    $real_url = $_SERVER['REQUEST_URI'];
    preg_match('/^([^\?]*)(\?.+)?$/i', $real_url, $real_matches);
    if(FORM_PAGE == $real_matches[1])
    {
        show_signup_page();
    }

    if (SUCCESS_URL == $real_matches[1]){
        create_user_and_login();
    }
    if (LOGIN_PAGE == $real_matches[1]){
        show_login_page(NULL);
    }
    if (ERROR_URL == $real_matches[1]){
        show_login_page(true);
    }

}

function show_login_page($error){
    get_header();
    echo '<h2 id="h2_login">Login Form</h2>';
    if ($error) echo '<div id="error_login">Incorrect user name or password - please try again<br/>Please click <a href="https://www.123signup.com/profile?Org=wtf">here</a> to reset your password</div>';
    show_just_form();
    echo '<style>
            #form{
                width: 325px;
                margin: 100px auto;
            }
            #form tr{
                border: 1px solid #fff;
            }
            #error_login{
                color: red;
                text-align: center;
                margin: 40px 0 -40px 0;
            }
            #form tr td{
                border: none !important;
            }
            #form tr input{
                margin-left: 10px;
                padding: 7px;
                border-radius: 3px;
                border: 1px solid #2ea3f2;
            }
            #form #submit_btn{
                margin-left: 0px;
                margin-top: 10px;
                border: none;
                cursor: pointer;
                min-width: 80px;
            }
            #h2_login{
                text-align: center;
                margin: 20px 0px -20px 0px;
            }
        </style>';

    get_footer();
    die();
}



add_action( 'init', 'signup_run' );


function shortcode_func( $atts ) {
    wp_enqueue_script('jquery');
    ?>
    <p>
        <a href="<?php echo FORM_PAGE;?>" id="open_123signup"><img src="/wp-content/plugins/123signup/123_logo.jpg" alt="123signup login"/></a>
    </p>
    <script>
        window.onload = function(){
            jQuery('#open_123signup').click(function(e){
                e.preventDefault();
                var strWindowFeatures = "location=yes,resizable=yes,scrollbars=yes,status=yes,width=600, height=300";
                var myWindow = window.open("<?php echo FORM_PAGE;?>", "", strWindowFeatures);
            })
        };
    </script>
    <style>
        #open_123signup{
            outline: none;
        }
        #open_123signup img{
            width: 50px;
            height:50px;
            box-shadow: 0px 0px 2px #000;
            outline: none;
        }
    </style>
    <?php
}


add_action( 'pre_get_posts', 'exclude_category' );
function exclude_category( $query ) {
    global $post;
    global $wpdb;
    $cat_id =  get_cat_id('private');
    if (isset( $_COOKIE['user123']) && strpos($_COOKIE['user123'], SALT)) {
        return;
    } else{
        if ($query->is_search) {
            $query->set('post_type', 'post');
        }
        if (is_category($cat_id)) {wp_redirect(LOGIN_PAGE);die();}
        if (is_page('private')){
            $query->set('page_id', array(0));
            wp_redirect(LOGIN_PAGE);
            die();
        }

        if (is_page()){
            $page = get_page_by_path($query->query_vars['pagename']);
            $parents = get_post_ancestors( $page->ID );
            $id = ($parents) ? $parents[count($parents)-1]: $post->ID;
            $p = get_post($id);
            if($p->post_name == 'private'){
                $query->set('page_id', array($id));;
                wp_redirect(LOGIN_PAGE);
                die();
            }
        }

    $not = '-'.$cat_id;
    $query->set('cat', $not);
    $tag_id = get_term_by('name', 'private', 'post_tag');
    $args = array($tag_id->term_id); //id of 'regular' tag in custom post type 'event'
    $query->set('tag__not_in', $args);
    $query_byCat="
            SELECT ".$wpdb->prefix."posts.ID
            FROM ".$wpdb->prefix."posts, ".$wpdb->prefix."term_relationships, ".$wpdb->prefix."terms
            WHERE ".$wpdb->prefix."posts.ID = ".$wpdb->prefix."term_relationships.object_id
            AND ".$wpdb->prefix."terms.term_id = ".$wpdb->prefix."term_relationships.term_taxonomy_id
            AND ".$wpdb->prefix."terms.name = 'private'";

    $querystr = "
            SELECT p.ID
            FROM ".$wpdb->prefix."posts p
            INNER JOIN ".$wpdb->prefix."term_relationships tr ON (p.ID = tr.object_id)
            INNER JOIN ".$wpdb->prefix."term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
            INNER JOIN ".$wpdb->prefix."terms t ON (tt.term_id = t.term_id)
            WHERE tt.taxonomy = 'post_tag'
            AND t.slug IN ('private')
    ";

    $query_posts ="
            SELECT ".$wpdb->prefix."posts.ID AS ID
            FROM ".$wpdb->prefix."posts
            WHERE ".$wpdb->prefix."posts.post_status = 'publish'
            AND ".$wpdb->prefix."posts.ID IN (".$querystr.")
            OR ".$wpdb->prefix."posts.ID IN (".$query_byCat.")";

    $post_ids = array();
    $result= $wpdb->get_results($query_posts);
    foreach($result as $res){
        $post_ids[] = $res->ID;
    }
    $query->set('post__not_in', $post_ids);
    }
}
