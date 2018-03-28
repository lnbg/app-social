<template>
    <div id="facebook-post">
        <div class="box box-widget">
            <div class="box-header with-border">
                <div class="user-block">
                    <img class="img-circle" :src="facebookAnalytics.account_picture" alt="User Image">
                    <span class="username"><a href="#">{{ facebookAnalytics.account_name }}</a></span>
                    <span class="description">Shared publicly - {{ postInstance.facebook_created_at }}</span>
                </div>
                <!-- /.user-block -->
                <div class="box-tools">
                    <img class="img-circle" src="/imgs/best.jpeg" width="48px" height="48px" />
                </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <p v-html="postInstance.messages"></p>
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
            <!-- /.box-footer -->
        </div>
    </div>
</template>
<script>
export default {
    props: {
        post: {
            facebook_post_id: 0,
            facebook_analytics_id: 0,
            messages: String.empty,
            picture: String.empty,
            story: String.empty,
            reaction_like: 0,
            reaction_haha: 0,
            reaction_love: 0,
            reaction_wow: 0,
            reaction_sad: 0,
            reaction_angry: 0,
            shares: 0,
            comments: 0,
            facebook_created_at: String.empty
        },
        facebookAnalytics: {
            id: 0,
            account_id: 0,
            account_name: String.empty,
            account_username: String.empty,
            account_picture: String.empty,
            account_link: String.empty,
            total_posts: 0,
            total_page_followers: 0,
            total_page_likes: 0,
            total_posts_likes: 0,
            total_posts_loves: 0,
            total_posts_wows: 0,
            total_posts_sads: 0,
            total_posts_hahas: 0,
            total_posts_angries: 0,
            total_posts_shares: 0,
            total_posts_comments: 0,
            total_posts_thankfuls: 0,
            average_posts_per_day: 0,
            average_reactions_per_post: 0,
            average_intereactions_per_post: 0,
        },
    },
    computed: {
        postInstance() {
            console.log(this.post);
            return this.post;
        }
    }
}
</script>

