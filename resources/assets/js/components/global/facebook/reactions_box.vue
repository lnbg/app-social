<template>
    <div class="info-box widget-info-box">
        <span class="info-box-icon" :class="box_color">
            <img :src="icon" />
        </span>
        <div class="info-box-content">
            <span class="info-box-text">{{ title }}</span>
            <span class="info-box-number">{{ value | currency }}</span>
        </div>
    <!-- /.info-box-content -->
    </div>
</template>
<script>
export default {
    props: { box_color: String, icon: String, title: String, value: Number}
}
</script>

