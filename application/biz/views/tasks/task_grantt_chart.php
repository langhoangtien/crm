<?php $this->load->view("partial/header"); ?>

<script src="<?php echo base_url('assets/gantt') ?>/codebase/dhtmlxgantt.js?v=6.0.0"></script>
<link rel="stylesheet" href="<?php echo base_url('assets/gantt') ?>/codebase/dhtmlxgantt.css?v=6.0.0">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="<?php echo base_url('assets/gantt/samples') ?>/common/testdata.js?v=6.0.0"></script>



<form class="gantt_control">
    <input type="radio" id="scale1" class="gantt_radio" name="scale" value="1"/><label for="scale1"><i class="material-icons">radio_button_unchecked</i>Day scale</label>
    <input type="radio" id="scale2" class="gantt_radio" name="scale" value="2"/><label for="scale2"><i class="material-icons">radio_button_unchecked</i>Week scale</label>
    <input type="radio" id="scale3" class="gantt_radio" name="scale" value="3"/><label for="scale3"><i class="material-icons">radio_button_unchecked</i>Month scale</label>
    <input type="radio" id="scale4" class="gantt_radio" name="scale" value="4" checked/><label class="checked_label" for="scale4"><i class="material-icons icon_color">radio_button_checked</i>Year scale</label>
</form>
<div id="gantt_here" style='width:100%; height:1000px;'></div>
<script>

    function setScaleConfig(value) {
        switch (value) {
            case "1":
                gantt.config.scale_unit = "day";
                gantt.config.step = 1;
                gantt.config.date_scale = "%d %M";
                gantt.config.subscales = [];
                gantt.config.scale_height = 27;
                gantt.templates.date_scale = null;
                break;
            case "2":
                var weekScaleTemplate = function (date) {
                    var dateToStr = gantt.date.date_to_str("%d %M");
                    var startDate = gantt.date.week_start(new Date(date));
                    var endDate = gantt.date.add(gantt.date.add(startDate, 1, "week"), -1, "day");
                    return dateToStr(startDate) + " - " + dateToStr(endDate);
                };

                gantt.config.scale_unit = "week";
                gantt.config.step = 1;
                gantt.templates.date_scale = weekScaleTemplate;
                gantt.config.subscales = [
                    {unit: "day", step: 1, date: "%D"}
                ];
                gantt.config.scale_height = 50;
                break;
            case "3":
                gantt.config.scale_unit = "month";
                gantt.config.date_scale = "%F, %Y";
                gantt.config.subscales = [
                    {unit: "day", step: 1, date: "%j, %D"}
                ];
                gantt.config.scale_height = 50;
                gantt.templates.date_scale = null;
                break;
            case "4":
                gantt.config.scale_unit = "year";
                gantt.config.step = 1;
                gantt.config.date_scale = "%Y";
                gantt.config.min_column_width = 50;

                gantt.config.scale_height = 90;
                gantt.templates.date_scale = null;


                gantt.config.subscales = [
                    {unit: "month", step: 1, date: "%M"}
                ];
                break;
        }
    }

    setScaleConfig('4');

    gantt.init("gantt_here", new Date(2018, 0, 1), new Date(2018, 8, 1));
    gantt.parse(demo_tasks);

    var func = function (e) {
        e = e || window.event;
        var el = e.target || e.srcElement;
        var value = el.value;
        setScaleConfig(value);
        gantt.render();
    };

    var els = document.getElementsByName("scale");
    for (var i = 0; i < els.length; i++) {
        els[i].onclick = func;
    }
    

    var labelEls = document.getElementsByTagName("label");
    for (var i = 0; i < labelEls.length; i++) {
        labelEls[i].onclick = function() {
            updTargetEl(this, "checked_label");

            var labelEls = document.getElementsByTagName("label");
            updOtherEls(labelEls, this, "checked_label")

            var el = this.querySelector("i");
            updTargetEl(el, "icon_color");

            var iTagEls = document.getElementsByTagName("i");
            updOtherEls(iTagEls, el, "icon_color");
            updElsContent(iTagEls, el);
        }
    }

    function updTargetEl(el, className){
        if(el.classList.contains(className)) return

        el.classList.add(className);
    }

    function updOtherEls(arr, targetEl, className){
        for (var i = 0; i < arr.length; i++) {
            if(arr[i] != targetEl && arr[i].classList.contains(className))
                arr[i].classList.remove(className);
        }
    }

    function updElsContent(arr, targetEl){
        for (var i = 0; i < arr.length; i++) {
            arr[i].textContent = arr[i]==targetEl?"radio_button_checked":"radio_button_unchecked";
        }
    }
</script>

<?php $this->load->view("partial/footer"); ?>