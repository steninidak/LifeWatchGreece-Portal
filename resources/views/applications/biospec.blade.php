<div class='row'>
    
    <div class='col-md-2'>
         
    </div>
    <div class='col-md-7'>
    
        <div style='border: 1px solid gray; padding:5px 10px; background-color: #DDEEFF; text-align: left; font-weight: bold'>Register/Unregister to our mailing list</div>
        <div style='border-bottom: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray; padding: 20px 0px; background-color: #F0F3FF'>
        {{ Form::open(array('url'=>'','class'=>'form-horizontal','id'=>'subscription_form')) }}

            {{ $errors->first('generic',"<span style='color:red'>:message</span>") }}

            <div class="form-group">
                <label for="fullname" class="col-sm-4 control-label">Full name *</label>
                <div class="col-sm-7">
                  {{ Form::text('fullname',Input::old('fullname'),array('class'=>'form-control')) }}
                  {{ $errors->first('fullname',"<span style='color:red'>:message</span>") }}
                </div>            
             </div>                   

            <div class="form-group">
                <label for="email" class="col-sm-4 control-label">E-mail *</label>
                <div class="col-sm-7">
                  {{ Form::text('email',Input::old('email'),array('class'=>'form-control')) }}
                  {{ $errors->first('email',"<span style='color:red'>:message</span>") }}
                </div>
             </div>            

             <div class="form-group">
                <label for="captcha" class="col-sm-4 control-label">Fill in the image text: *</label>
                <div class="col-sm-7" id="captcha_table">
                  {{ Form::text('captcha','',array('class'=>'form-control','style'=>'width: 100px; display: inline-block !important')) }}
                  {!! captcha_img() !!}
                    <div title="Refresh image" class="btn btn-sm btn-default" onclick="javascript:refresh_captcha()"><span class="glyphicon glyphicon-repeat"></span></div>
                  {{ $errors->first('captcha',"<span style='color:red'>:message</span>") }}
                </div>
             </div>                      

            <input type='hidden' name='stamp' value='{{ (new DateTime())->modify('next day')->format('Y-m-d H:i:s') }}'>

            {{ Form::close() }}

            <div style='text-align: center'>
                <button class='btn btn-primary' id='subscribe_button'>Register</button>
                <button class='btn btn-danger' id='unsubscribe_button'>Un-Register</button>
            </div>
        </div>
     </div>
</div>

<script type='text/javascript'>
    
    function refresh_captcha(){
        var formURL = "{{ url('new_captcha_link') }}";
        $.get(formURL).done(function( data ) {
                $('#captcha_table img').attr('src',data);
            }
        );
    }
    
    $('#subscribe_button').on('click',function(){
        $('#subscription_form').prop('action','<?php echo url('biospec/subscribe');  ?>');
        $('#subscription_form').submit();
    });
    $('#unsubscribe_button').on('click',function(){
        $('#subscription_form').prop('action','<?php echo url('biospec/unsubscribe');  ?>');
        $('#subscription_form').submit();
    });
    
</script>

<?php

    $areas = array(
        'SPHNC' =>  array(
            'How To'    =>  array(
                'url'  =>  'http://www.spnhc.org/28/how-to',
                'links'  =>  array(
                    'Introduction to insect storage techniques for small collections'   =>  'http://www.spnhc.org/media/assets/How_To_1.pdf',
                    'How to prepare seaweed specimens'  =>  'http://www.spnhc.org/media/assets/How_To_2.pdf',
                    'How to pack a herbarium specimen for loan' =>  'http://www.spnhc.org/media/assets/How_To_3.pdf'
                )
            ),
            'Books'     =>   array(
                'url'   =>  'http://www.spnhc.org/30/reference-books',
                'links' =>  array(
                    'Health and Safety for Museum Professionals'    =>  '',
                    'Museum Studies: Perspectives & Innovations'    =>  '',
                    'Managing the Modern Herbarium'                 =>  '',
                    'Storage of Natural History Collections: A Preventive Conservation Approach'    =>  '',
                    'Storage of Natural History Collections: Ideas and Practical Solutions'         =>  ''
                )
            ),
            'Periodic' =>  array(
                'url'   =>  '',
                'links' =>  array(
                    'Collection Forum'  =>  'http://www.spnhc.org/20/collection-forum'                    
                )
            ),
            'Leaflets'  =>  array(
                'url'   =>  'http://www.spnhc.org/26/leaflets',
                'links' =>  array(
                    'Anoxic Microenvironments: A Simple Guide'  =>  'http://www.spnhc.org/media/assets/leaflet1.pdf',
                    'Adhesives and Consolidants in Geological and Paleontological Applications' =>  '',
                    'Introduction, Guide, Health and Safety, Definitions'   =>  'http://www.spnhc.org/media/assets/leaflet2_descrip.pdf',
                    'Wall Chart'    =>  'http://www.spnhc.org/media/assets/leaflet2_chart.pdf',
                    'Guide to the Identification of Common Clear Plastic Films' =>  'http://www.spnhc.org/media/assets/leaflet3.pdf',
                    'Comparison of Temperature and Relative Humidity Dataloggers for Museum Monitoring' =>  'http://www.spnhc.org/media/assets/leaflet4.pdf',
                    'Distinguishing between Ethanol and Isopropanol in Natural History Collection Fluid Storage'    =>  'http://www.spnhc.org/media/assets/leaflet5.pdf',
                    'The Restoration Of A Human Skeleton - As A Scientific Specimen'    =>  'http://www.spnhc.org/media/assets/leaflet6.pdf'                   
                )
            ),
            'Reports'   =>  array(
                'url'   =>  '',
                'links' =>  array(
                    'Report on the 2014 Food in Museums Survey produced by the SPNHC Conservation Committee'    =>  'http://www.spnhc.org/media/assets/SPNHCFoodSurveyReport_2014.pdf',
                    'Libraries and Museums in an Era of Participatory Culture'  =>  'http://www.spnhc.org/media/assets/SGS_Report_2012.pdf',
                    'Best Practices-what does that imply?'  =>  'http://www.spnhc.org/media/assets/cato_BP.pdf',
                    'Consortium of European Taxonomic Facilities: Code of Conduct and Best Practice for Access and Benefit‐Sharing' =>  'http://cetaf.org/sites/default/files/cetaf_abs_code_of_conduct_2014.pdf',
                    'National Museum of Natural History, Smithsonian Institution ABS policy'    =>  'http://www.mnh.si.edu/rc/cp/_docs/NMNH_ABS_policy.pdf',
                    'Access and Benefit Sharing - Global Implications for Biodiversity Research, Collections and Collection Management Arising from the Nagoya Protocol'    =>  'http://www.spnhc.org/media/assets/ABS-GlobalImplications_SPNHC-Sep2014Vol28.pdf'
                )
            )
        ),
        'AAAM'  =>  array(
            'Bookstore' =>  array(
                'url'   =>  'http://www.aam-us.org/resources/bookstore',
                'links' =>  array()
            ),
            'Books' =>  array(
                'url'   =>  '',
                'links' =>  array(
                    'Care and Conservation of Natural History Collections'  =>  'https://books.google.fr/books?id=ffgTAQAAIAAJ',
                    'Natural History Museums: Directions for Growth'        =>  'https://books.google.fr/books?id=lZ9z3KfxStEC'
                )
            ),
            'SpecIss'   =>  array(
                'url'   =>  '',
                'links' =>  array(
                    'No specimen left behind: mass digitization of natural history collections' =>  'http://zookeys.pensoft.net/browse_journal_issue_documents.php?issue_id=361'                   
                )
            ),
            'Proc'   =>  array(
                'url'   =>  '',
                'links' =>  array(
                    'Papers presented at a Symposium on Natural History Collections, Past, Present, Future' =>  'http://www.biodiversitylibrary.org/page/34550107#page/167/mode/1up'                   
                )
            ),
            'Periodic'   =>  array(
                'url'   =>  '',
                'links' =>  array(
                    'e-conservation Journal' =>  '',
                    'Museum pest identification'    =>  'http://museumpests.net/identification/'
                )
            )
        ),
        'AAAM'  =>  array(
            'Uncategorized' => array(
                'url'   =>  '',
                'links' =>  array(
                    'Fact Sheets on Scientific Collections' =>  'http://nscalliance.org/?page_id=10',
                    'On the Importance of Scientific Collections'   =>  'http://nscalliance.org/?page_id=10'
                )
            )
        ),
        'iDigBio'  =>  array(
            'Books' =>  array(
                'url'   =>  '',
                'links' =>  array(
                    'Many guides'   =>  'https://www.idigbio.org/tags/documentation',
                    'Biological Collections Databases, Tools, and Data Publication Portals' =>  'https://www.idigbio.org/content/biological-collections-databases',
                    'DINA'  =>  'http://www.dina-project.net/',
                    'Specify'   =>  'http://specifyx.specifysoftware.org/',
                    'BioLink'   =>  'https://code.google.com/p/biolink/wiki/BioLink/',
                    'Biota'     =>  'http://viceroy.eeb.uconn.edu/Biota/Biota2Pages/about_biota.html#AboutFeatures',
                    'Commercial'=>  '',
                    'Proficio'  =>  'www.rediscoverysoftware.com/'
                )
            )
        )
    );

?>

<style type="text/css">
    
    .area_div {
        font-size: 16px;
        font-weight: bold;
        color: #843534;
        margin: 10px 0px;
    }
    
    table.table {
        border: 0px;
    }
    
    table.table tr td {
        text-align: left;
        border: 0px;
        padding-left: 30px;
    }
    
</style>

@foreach($areas as $areaTitle => $areaInfo)
    <div class='area_div'>{{ $areaTitle }}</div>
    <div style='margin-left: 30px'>
        @foreach($areaInfo as $unitTitle => $unitInfo)
            <table class="table table-bordered table-condensed">
                @if(empty($unitInfo['url']))
                    <caption>{{ $unitTitle }}</caption>
                @else
                    <caption><a href="http://www.spnhc.org/28/how-to">{{ $unitTitle }}</a></caption>
                @endif
                <tbody>
                    @foreach($unitInfo['links'] as $linkText => $linkUrl)
                    <tr>
                        <td>
                        @if(empty($linkUrl))
                            {{ $linkText }}
                        @else
                            <a href="{{ $linkUrl }}">{{ $linkText }}</a>
                        @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach    
    </div>
@endforeach