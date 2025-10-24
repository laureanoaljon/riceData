<!-- Fixed Container for Left Controls -->
<div class="side-panel">

    <div class="row">   
        <!-- Button 1 -->
        <button class="btn btn-success action-button d-flex align-items-center justify-content-between w-100" id="load_pay">
            <div class="d-flex align-items-center">
                <div class="icon-container text-white me-2"><i class="fa fa-line-chart"></i></div>
                <div class="text-container">Production, Area and Yield Map</div>
            </div>
            <span><i class="fa fa-chevron-down" aria-hidden="true"></i></span>
        </button>

        <!-- Map controls -->
        <div class="map-layer-controls" id="pay_layer_controls">
            <p><b>Select Filter:</b></p>

            <div class="container-fluid px-3 mb-2">
                <!-- Radio buttons -->
                <div class="row mt-2 mb-3 text-left">
                    <div class="col-12 d-flex gap-0">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pay_layers" id="layer_production" value="production" checked>
                            <label class="form-check-label" for="layer_production">Production</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pay_layers" id="layer_area" value="area_harvested">
                            <label class="form-check-label" for="layer_area">Area Harvested</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="pay_layers" id="layer_yield" value="yield">
                            <label class="form-check-label" for="layer_yield">Yield</label>
                        </div>
                    </div>
                </div>

                <!-- Year -->
                <div class="row align-items-center mb-3 mt-2">
                    <div class="col-3">
                    <h6 class="mb-0 form-label">Year:</h6>
                    </div>
                    <div class="col-9">
                    <select class="form-select form-select-sm" id="payYearFilter">
                        <option class="text-success" value="" disabled>Select Option</option>
                        <option value="2024" selected>2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                        <option value="2020">2020</option>
                        <option value="2019">2019</option>
                        <option value="2018">2018</option>
                        <option value="2017">2017</option>
                        <option value="2016">2016</option>
                        <option value="2015">2015</option>
                    </select>
                    </div>
                </div>

                <!-- Period -->
                <div class="row align-items-center mb-3 mt-2">
                    <div class="col-3">
                    <h6 class="mb-0 form-label">Period:</h6>
                    </div>
                    <div class="col-9">
                    <select class="form-select form-select-sm" id="payPeriodFilter">
                        <option class="text-success" value="" disabled>Select Option</option>
                        <option value="1" data-year-type="1" selected>Annual</option>
                        <option value="1" data-year-type="2">Semester 1</option>
                        <option value="2" data-year-type="2">Semester 2</option>
                        <option value="1" data-year-type="3">Quarter 1</option>
                        <option value="2" data-year-type="3">Quarter 2</option>
                        <option value="3" data-year-type="3">Quarter 3</option>
                        <option value="4" data-year-type="3">Quarter 4</option>
                    </select>
                    </div>
                </div>

                <!-- Another Period (example) -->
                <div class="row align-items-center mb-3 mt-2">
                    <div class="col-3">
                    <h6 class="mb-0 form-label">Ecosystem:</h6>
                    </div>
                    <div class="col-9">
                    <select class="form-select form-select-sm" id="payEcosystemFilter">
                        <option class="text-success" value="" disabled>Select Option</option>
                        <option value="2" selected>All Ecosystems</option>
                        <option value="1">Irrigated Areas</option>
                        <option value="0">Non-Irrigated Areas</option>      
                    </select>
                    </div>
                </div>

            </div>

            
            <div class="text-center mt-4 mb-3">
                <button class="btn btn-success btn-sm w-50 add-btn" id="addBtn" style="border-radius: 12px;" data-layer="pay">
                    <i class="fa fa-plus"></i> &emsp;Add
                </button>
            </div>
        </div>
    </div> 

    <div class="row mt-2">
        <!-- Button 2 -->
        <button class="btn btn-success action-button d-flex align-items-center justify-content-between w-100" id="load_pad">
            <div class="d-flex align-items-center">
                <div class="icon-container text-white me-2"><i class="fa fa-area-chart"></i></div>
                <div class="text-container">Production and Demand</div>
            </div>
            <span><i class="fa fa-chevron-down" aria-hidden="true"></i></span>
        </button>

        <div class="map-layer-controls" id="pad_layer_controls" style="max-height: 700px; overflow-y: auto;">

            <div class="container-fluid px-4 mb-2">

                <!-- Radio Buttons -->
                <div class="row mt-2 mb-3 text-left">
                    <div class="col-12 mt-3 d-block" id="radioCollapseGroup">

                        <!-- Radio 1 -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pad_layers" id="rad_output_demand"
                                data-bs-toggle="collapse" data-bs-target="#data_output_demand" aria-expanded="false">
                                <label class="form-check-label" for="rad_output_demand">Rice Output vs Demand</label>
                            </div>
                            <div class="collapse mt-2" id="data_output_demand" data-bs-parent="#radioCollapseGroup">
                                <div class="card card-body bg-light mb-2">
                                    <strong>Provincial Rice Output vs Demand:</strong> Total rice production per province compared to estimated consumption or demand. <br>
                                    <strong>Regionla Rice Output vs Demand:</strong> Total rice production per region compared to estimated consumption or demand.
                                </div>
                            </div>
                        </div>

                        <!-- Radio 2 -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pad_layers" id="rad_double_dry"
                                data-bs-toggle="collapse" data-bs-target="#data_double_dry" aria-expanded="false">
                                <label class="form-check-label" for="rad_double_dry">Double Dry Cropping & Special Projects</label>
                            </div>
                            <div class="collapse mt-2" id="data_double_dry" data-bs-parent="#radioCollapseGroup">
                                <div class="card card-body bg-light mb-2">
                                <strong>Double Dry Cropping:</strong> Production data from double dry cropping cycles and other special rice production initiatives.
                                </div>
                            </div>
                        </div>

                        <!-- Radio 3 -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pad_layers" id="rad_cost_prod"
                                data-bs-toggle="collapse" data-bs-target="#data_cost_prod" aria-expanded="false">
                                <label class="form-check-label" for="rad_cost_prod">Cost of Production</label>
                            </div>
                            <div class="collapse mt-2" id="data_cost_prod" data-bs-parent="#radioCollapseGroup">
                                <div class="card card-body bg-light mb-2">
                                <strong>Cost of Production:</strong> Average cost of rice production by ecosystem type and cropping season, per region and province.
                                </div>
                            </div>
                        </div>

                        <!-- Radio 4 -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pad_layers" id="rad_intput_distri"
                                data-bs-toggle="collapse" data-bs-target="#data_input_distri" aria-expanded="false">
                                <label class="form-check-label" for="rad_intput_distri">Input Distribution Status</label>
                            </div>
                            <div class="collapse mt-2" id="data_input_distri" data-bs-parent="#radioCollapseGroup">
                                <div class="card card-body bg-light mb-2">
                                <strong>Input Distribution Status:</strong> Status of seeds and fertilizer distribution.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


            </div>

            
            <div class="text-center mt-4 mb-3">
                <button class="btn btn-success btn-sm w-50 add-btn" id="addBtnPAD" style="border-radius: 12px;" data-layer="pay">
                    <i class="fa fa-plus"></i> &emsp;Add
                </button>
            </div>
        </div>
    </div>

    <!-- <button class="btn btn-success action-button" id="load_land">
        <div class="icon-container"><i class="fa fa-mountain"></i></div>
        <div class="text-container">Land Maps</div>
    </button>

    <div class="map-layer-controls" id="land_layer_controls">
        <p><b>Select Layer:</b></p>
        <div class="form-check">
        <input class="form-check-input" type="radio" name="land_layers" id="land1" value="land_cover" checked>
        <label class="form-check-label" for="land1">Land Cover</label>
        </div>
        <div class="form-check">
        <input class="form-check-input" type="radio" name="land_layers" id="land2" value="soil_type">
        <label class="form-check-label" for="land2">Soil Type</label>
        </div>
        <div class="form-check">
        <input class="form-check-input" type="radio" name="land_layers" id="land3" value="soil_nutrients">
        <label class="form-check-label" for="land3">Soil Nutrient</label>
        </div>
        <button class="btn btn-success-info btn-sm w-100 mt-2" id="add_land_map">
        <i class="fas fa-circle-plus"></i> Add Map
        </button>
    </div> -->

    <!-- Other buttons (no controls yet) -->
    <!-- <button class="btn btn-success action-button" id="load_water">
        <div class="icon-container"><i class="fa fa-water"></i></div>
        <div class="text-container">Water Resource Maps</div>
    </button>

    <button class="btn btn-success action-button" id="load_climate">
        <div class="icon-container"><i class="fa fa-cloud-sun"></i></div>
        <div class="text-container">Climate Maps</div>
    </button>

    <button class="btn btn-success action-button" id="load_damages">
        <div class="icon-container"><i class="fa fa-triangle-exclamation"></i></div>
        <div class="text-container">Damages Maps</div>
    </button> -->
</div>