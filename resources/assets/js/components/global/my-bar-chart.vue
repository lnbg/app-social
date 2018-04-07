<template>
    <div class="my-barchart">
        <canvas id="myCanvas" style="width: 350"></canvas>
    </div>
</template>
<script>
export default {
    mounted() {
        var myCanvas = document.getElementById("myCanvas");
        myCanvas.width = 1000;
        myCanvas.height = 300;
        var ctx = myCanvas.getContext("2d");
        
        function drawBar(ctx, upperLeftCornerX, upperLeftCornerY, width, height, color){
            ctx.save();
            ctx.fillStyle=color;
            ctx.fillRect(upperLeftCornerX + 10, upperLeftCornerY, width, height);
            ctx.strokeRect(upperLeftCornerX + 10, upperLeftCornerY, width + 1, height + 1);
            ctx.restore();
        }

        drawBar(ctx, 0, 0, 200, 20, "blue");
        drawBar(ctx, 0, 30, 400, 20, "yellow");
        drawBar(ctx, 0, 60, 580, 20, "red");
        drawBar(ctx, 0, 90, 800, 20, "green");
    }
}
</script>
