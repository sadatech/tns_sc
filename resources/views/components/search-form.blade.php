<button type="button" class="filterButton pull-right js-arrow au-btn au-btn-icon au-btn--yellow au-btn--small" onclick="showContent()">
    <i class="fas fa-filter"></i>Filters
    <span class="arrow">
        <i class="fas fa-angle-down"></i>
    </span>
</button>
<div class="filterContent pull-right col-md-12" style="display: none;border: 1px solid #ccc;margin:5px 0 0 0;padding: 10px 15px;">
    <form></form>
    <form id="searchForm" class="au-form-icon--sm p-b-5" action="#" method="post">
        <div class="row">
            @foreach ($field as $f)
                <div class="col-md-4">
                    @php 
                        $placeholder0 = (isset($f['placeholder']) ? $f['placeholder'] : str_replace('_', ' ', $f['name'])); 
                        $placeholder = "Please enter ".$placeholder0." here";
                        $placeholderS = ucwords($placeholder0);
                    @endphp
                    @if(isset($f['type']))
                        @if($f['type'] == 'select')
                            {{ Form::select2Input($f['name'], null, $f['data'], ['useLabel' => true, 'labelText' => $placeholderS, 'formAlignment' => 'vertical']) }}
                        @endif
                    @else
                        {{ Form::textInput($f['name'], null, ['formAlignment' => 'vertical', 'placeholder' => $placeholder, 'labelText' => $placeholderS, 'data-toggle' => (isset($f['data-toggle']) ? $f['data-toggle'] : '' )])}}
                    @endif
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class=" pull-right au-btn au-btn-icon au-btn--red au-btn--small" onclick="searchClearFunction()" type="button">
                    <i class="zmdi zmdi-replay zmdi-hc-fw"></i> Clear
                </button>
                <button class=" pull-right au-btn au-btn-icon au-btn--blue au-btn--small" onclick="{{$searchFunction ?? 'searchFunction'}}('{{$url}}',{{isset($option) ?'true':'false'}})" type="button">
                    <i class="zmdi zmdi-search"></i> Search
                </button>
            </div>
        </div>
    </form>
</div>