新网帐号: ihelpoo/wobang00

diffusiontoowner %s扩散了你的消息，详情%s

"<a href='".__ROOT__."/mutual/rc/".$msg['url_id']."?please' target='_blank' class='a_view_info_sys'>详情</a>";


NOTICE_FORMAT_TEMPLATE
diffusiontoowner %s 扩散了你的这条消息 <a href='%s/%s' class='%s'> 详情</a>
diffusion %s 扩散了这条消息给你 <a href='%s/%s/%s' class='%s'> 去看看</a>

DETAIL_URL
stream/i-para:diffusion        item/say
stream/ih-para:diffusion       item/help
stream/i-para:diffusiontoowner item/say

HSET Notice:Message:Template diffusiontoowner "%s 扩散了你的这条消息 <a href='%s/%s/%s' class='%s'> 详情</a>"
HSET Notice:Message:Template diffusion "%s 扩散了这条消息给你 <a href='%s/%s/%s' class='%s'> 去看看</a>"
HSET Notice:Message:Template plus  "%s 赞了你发布的 <a href='%s/%s/%s' class='%s'> 详情</a>"




HSET Notice:Message:Link stream/i-para:diffusion "item/say"
HSET Notice:Message:Link stream/ih-para:diffusion "item/help"
HSET Notice:Message:Link stream/i-para:diffusiontoowner "item/say"
HSET Notice:Message:Link stream/ih-para:diffusiontoowner "item/help"


HSET Notice:Message:Link stream/i-para:plus "item/say"
HSET Notice:Message:Link stream/ih-para:plus "item/help"
