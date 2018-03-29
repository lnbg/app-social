<template>
    <div id="posts-timeline">
        <!-- timeline time label -->
        <section  v-for="timelineItem in source" :key="timelineItem.date">
            <ul class="timeline">
                <li class="time-label">
                    <span class="bg-red">
                        {{ timelineItem.date }}
                    </span>
                </li>
                <!-- /.timeline-label -->
                <!-- timeline item -->
                <li v-for="postInstance in timelineItem.posts" :key="postInstance.facebook_post_id">
                    <!-- timeline icon -->
                    <i class="fa fa-comments bg-yellow"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i>{{ postInstance.facebook_created_at }}</span>
                        <div class="box-header with-border">
                            <div class="user-block">
                                <img class="img-circle" :src="facebookAnalytics.account_picture" alt="User Image">
                                <span class="username"><a href="#">{{ facebookAnalytics.account_name }}</a></span>
                                <span class="username" style="font-size: 12px">
                                    <a href="#"><i class="fa fa-facebook-square"></i> / {{ facebookAnalytics.account_username }}</a>
                                </span>
                            </div>
                        <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <p class="post-content" v-html="postInstance.messages"></p>
                            <p class="post-image"><img :src="postInstance.picture" /></p>
                        </div>
                        <!-- /.box-body -->
                        <!-- /.box-footer -->
                        <div class="box-footer">
                            <span class="pull-left text-muted">
                                <span><i class="fa fa-comment"></i> {{ postInstance.comments | currency }}</span>&nbsp;
                                <span><i class="fa fa-share"></i> {{ postInstance.share | currency }}</span>
                            </span>
                            <span class="pull-right text-muted">
                                <span><img class="reactions-img" src="/imgs/reactions/like.gif" height="16px" style="margin-left: 0;" />{{ postInstance.reaction_like | currency }}</span>
                                <span><img class="reactions-img"  src="/imgs/reactions/love.gif" height="16px" /> {{ postInstance.reaction_love | currency }}</span>
                                <span><img class="reactions-img"  src="/imgs/reactions/wow.gif" height="16px" /> {{ postInstance.reaction_wow | currency }}</span>
                                <span><img class="reactions-img"  src="/imgs/reactions/sad.gif" height="16px" /> {{ postInstance.reaction_sad | currency }}</span>
                                <span><img class="reactions-img"  src="/imgs/reactions/haha.gif" height="16px" /> {{ postInstance.reaction_hah | currency }}</span>
                                <span><img class="reactions-img"  src="/imgs/reactions/angry.gif" height="16px" /> {{ postInstance.reaction_angry | currency }}</span>
                            </span>
                        </div>
                    </div>
                </li>
            </ul>
        </section>
    </div>
</template>
<script>
export default {
    props: {source: Array, facebookAnalytics: Object},
}
</script>

