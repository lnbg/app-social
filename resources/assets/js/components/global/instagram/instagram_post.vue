<template>
    <div class="instagram-post">
        <div class="box box-widget">
            <div class="box-header with-border">
                <div class="user-block">
                    <img class="img-circle" :src="instagramProfile.picture" alt="User Image">
                    <span class="username"><a href="#">{{ instagramProfile.name }}</a></span>
                    <span class="description">Shared publicly - {{ mediaInstance.instagram_created_at }}</span>
                </div>
                <!-- /.user-block -->
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <p class="post-content" v-html="mediaInstance.caption"></p>
                <p class="post-image">
                    <img v-if="mediaInstance.media_type == 'image'" :src="mediaInstance.media_picture" />
                    <video v-if="mediaInstance.media_type == 'video'" width="320" height="240" controls>
                        <source :src="mediaInstance.media_picture" type="video/mp4">
                        <source src="movie.ogg" type="video/ogg">
                    </video>
                </p>
            </div>
            <!-- /.box-body -->
            <!-- /.box-footer -->
            <div class="box-footer">
                <span class="pull-left text-muted">
                    <span><i class="fa fa-comment"></i> {{ mediaInstance.comments | currency }}</span>&nbsp;
                    <span><i class="fa fa-heart"></i> {{ mediaInstance.like | currency }}</span>
                </span>
            </div>
            <!-- /.box-footer -->
        </div>
    </div>
</template>
<script>
export default {
    props: {
        instagramMedia: {
            instagram_analytics_id: 0,
            media_id: 0,
            meida_type: String.empty,
            media_code: String.empty,
            media_picture: String.empty,
            caption: String.empty,
            url: String.empty,
            dimension: String.empty,
            likes: 0,
            comments: 0,
            location: String.empty,
            tags: String.empty,
            instagram_created_at: String.empty
        },
        instagramProfile: {
            id: 0,
            instagram_id: 0,
            name: String.empty,
            user_name: String.empty,
            picture: String.empty,
            link: String.empty,
            website: 0,
            biography: String.empty,
            followers_count: 0,
            follows_count: 0,
            media_counts: 0,
        },
    },
    computed: {
        mediaInstance() {
            return this.instagramMedia;
        }
    }
}
</script>

