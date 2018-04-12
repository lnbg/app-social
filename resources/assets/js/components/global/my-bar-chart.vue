<template>
    <div class="my-barchart">
        <canvas id="myCanvas" style="width: 350"></canvas>
    </div>
</template>
<script>
export default {
    mounted() {
        var myCanvas = document.getElementById("myCanvas");
        myCanvas.height = 500;
        myCanvas.width = 1000;
        var ctx = myCanvas.getContext("2d");
        
        function drawChart(ctx, upperLeftCornerX, upperLeftCornerY, width, height, color, src){
            ctx.beginPath();
            ctx.fillStyle=color;
            let X = upperLeftCornerX + 10
            let Y = upperLeftCornerY
            let imageObj = new Image();
            imageObj.src = src
            imageObj.onload = function() {
                ctx.drawImage(imageObj, X, 0, 64, 64);
            }
            ctx.fillRect(upperLeftCornerX + 10, upperLeftCornerY, width, height);
            ctx.stroke();
        }
        var data = [350, 280, 185, 400];
        var src = [
            "https://scontent.xx.fbcdn.net/v/t31.0-1/p960x960/29060213_2156887361211324_2114750486757536299_o.jpg?_nc_cat=0&oh=5f9b75c742845caed12b42de2095bc9a&oe=5B6BF89A",
            "https://scontent.xx.fbcdn.net/v/t31.0-1/p960x960/27993081_2019314628291637_862590473080049619_o.jpg?_nc_cat=0&oh=2dc40dbbe401e38a2594c9c61df40ec8&oe=5B372E59",
            "https://scontent.xx.fbcdn.net/v/t1.0-1/29572886_948301132007530_5464272724620595350_n.jpg?_nc_cat=0&oh=2ca7ed843ffa522a7367912d81e1b2fe&oe=5B394E03",
            "https://scontent.xx.fbcdn.net/v/t1.0-1/29257651_1824963450880706_1018149893329387520_n.jpg?_nc_cat=0&oh=04c598d723599d36b5e2cff25c51529b&oe=5B6A783C"
        ];
        var MyBarChart = {
            draw: function() {
                data.forEach((element, index) => {
                    drawChart(ctx, (index + 1) * 70, 500, 50, -element, "rgb(0, 166, 90)", src[index]);
                });
            }
        }
        MyBarChart.draw();

    }
}
</script>
