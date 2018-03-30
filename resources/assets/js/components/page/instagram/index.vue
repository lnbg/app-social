<template>
    <div id="fanpage-wrapper">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Add new instagram profile</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <input type="text" v-model="instagramProfileLink" class="form-control" style="width: 400px; display: inline-block;" placeholder="Link to instagram profile" />
                <input type="button" @click="_createNewInstagramProfile" style="display: inline-block; margin-top: -5px; padding-left: 10px; padding-right: 10px;" class="btn btn-primary" value="Add">
            </div>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">INSTAGRAM PROFILES</h3>
                <div class="box-tools">
                    <a class="btn btn-box-tool" title="Fetching"><i class="fa fa-download"></i> Analytics</a>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="col-md-4" v-for="instagramProfile in lstInstagramProfileAnalytics" :key="instagramProfile.account_id">
                    <box-instagram-profile :profile="instagramProfile"></box-instagram-profile>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import BoxInstagramProfile from '../../global/instagram/box_profile'
import { mapState, mapActions } from 'vuex'

export default {
    name: 'Page_Instagram__Index',
    components: {
        'box-instagram-profile': BoxInstagramProfile
    },
    data() {
        return {
            instagramProfileLink: String.empty,
            orderBy: 1
        }
    },
    computed: {
        ...mapState('instagram', [
            'lstInstagramProfileAnalytics'
        ]),
    },
    methods: {
        ...mapActions('instagram', [
            'createNewInstagramProfile',
            'getListInstagramProfileAnalytics',
        ]),
        _createNewInstagramProfile() {
            this.createNewInstagramProfile(this.instagramProfileLink)
                .then(response => {
                    this.getListInstagramProfileAnalytics()
                })
        },

    },
    beforeCreate() {
        this.$store.dispatch('instagram/getListInstagramProfileAnalytics')
    },
    created() {
    }
}
</script>
