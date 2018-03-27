<template>
    <div id="fanpage-wrapper">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Add new instagram profile</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <input type="text" v-model="instagramLink" class="form-control" style="width: 400px; display: inline-block;" placeholder="Link to profile" />
                <input type="button" @click="createNewProfile" style="display: inline-block; margin-top: -5px; padding-left: 10px; padding-right: 10px;" class="btn btn-primary" value="Add">
            </div>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Data Analytics</h3>
                <div class="box-tools">
                    <a class="btn btn-box-tool" title="Fetching"><i class="fa fa-download"></i> Analytics</a>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'
import BoxFacebookPage from '../../global/facebook/box_page'

export default {
    name: 'Page_Instagram__Index',
    components: {
        'box-facebook-page' : BoxFacebookPage
    },
    data() {
        return {
            instagramLink: String.empty,
            orderBy: 1
        }
    },
    computed: {
        ...mapState('facebook', [
            'lstFacebookPageAnalytics'
        ]),
    },
    methods: {
        ...mapActions('facebook', [
            'createNewFacebookPage',
            'getListFacebookPageAnalytics'
        ]),
        createNewProfile() {
            this.createNewFacebookPage(this.facebookPageLink)
                .then(response => {
                    this.getListFacebookPageAnalytics()
                })
        },
    },
    beforeCreate() {
    },
    created() {
        
    }
}
</script>
