<?php
/*
 * Plugin Name: sktextadd
 * Plugin URI: http://devdiary.komish.jp/
 * Description: 記事の下部に設定したテキストを追加する。
 * Author: Komiya Shuichi
 * version: 0.1.1
 * Author URI: http://devdiary.komish.jp/
 */


/*
2017/09/15 version 0.1.1 

・クラスのコンストラクタの関数名を__construct に変更
*/

function get_html ( $str ) {
    return stripslashes(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
}
class sktextadd_options_t {
    private $options;
    
    function __construct() {
        $this->options = get_option( 'sktextadd_options', $this->default_options() );
    }
    
    function get_options() {
        return $this->options;
    }
    
    function default_options() {

        $options = array();
        $options["add_text"] = "";

        return $options;
    }
    
    function update( $data ) {
        
        $options['add_text'] = $data['add_text'];
        update_option("sktextadd_options", $options);

        $this->options = $options;
        
        return $options;
    }
}

//--------------------------------------------------------------------------
//
//  管理画面>設定>Exsampleプラグインページを追加
//
//--------------------------------------------------------------------------
  
class sktextadd_admin_menu_t {
    private $opt;
    
    function __construct() {
        $this->opt = new sktextadd_options_t();
    }
    
    function exec() {
        if ( isset( $_POST['save'] ) ) {
            $options = $this->opt->update( $_POST );
            echo '<div class="updated"><p><strong>保存しました</strong></p></div>';
        } else {
            $options = $this->opt->get_options();
        }
        // 設定変更画面を表示する
        ?>
        <div class="wrap">
            <h2>sktextadd</h2>
            <form method="post" action="<?php echo get_html( $_SERVER['REQUEST_URI'] ); ?>">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">追加するテキスト:</th>
                        <td><textarea name="add_text" cols="90" rows="10"><?php echo get_html( $options["add_text"] ); ?></textarea></td>
                    </tr>
                </table>
                <p class="submit">
            <input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes') ?>" />
                </p>
            </form>
        </div>
        <?php
    }
}

function sktextadd_options_menu() {
    $admin = new sktextadd_admin_menu_t();
    
    $admin->exec();
}

// アクションフックのコールバッック関数
function add_sktextadd_admin_menu() {
    // 設定メニュー下にサブメニューを追加:
    add_options_page('sktextadd', 'sktextadd', 'manage_options', __FILE__, 'sktextadd_options_menu');
}

// 管理メニューのアクションフック
add_action('admin_menu', 'add_sktextadd_admin_menu');
  
//--------------------------------------------------------------------------
//
//  プラグイン削除の際に行うオプションの削除
//
//--------------------------------------------------------------------------
if ( function_exists('register_uninstall_hook') ) {
    register_uninstall_hook(__FILE__, 'uninstall_hook_sktextadd');
}
function uninstall_hook_sktextadd () {
    delete_option('sktextadd_options');
}

class sktextadd_t {
	private $options;

    public function __construct() {
    	$this->plugin_name = 'sktextadd';
        $opt = new sktextadd_options_t();
        $this->options = $opt->get_options();
   }

   public function exec( $content ) {
		
		if ( is_single() && ( get_post_format() === false )) {
			$text = stripslashes($this->options["add_text"]);
			return $content . $text;
		} else {
			return $content;
		}
   }
}

function sktextadd_exec( $content ){
    $sktextadd = new sktextadd_t();
    return $sktextadd->exec( $content );
}

add_filter('the_content', 'sktextadd_exec', 9);

?>
