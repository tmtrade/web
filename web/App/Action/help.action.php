<?
/**
 * 商标类别诠释
 *
 * @package Action
 * @author  Far
 * @since   2016/04/12
 */
class HelpAction extends AppAction
{
  public function index()
    {
        $this->set("s_url",GUANJIA_URL);
        $this->setSeo(14);
        $this->display();
    }
}
?>