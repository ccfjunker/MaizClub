<script src="//cidades-estados-js.googlecode.com/files/cidades-estados-1.2-utf8.js"></script>
<script type="text/javascript">

function getCountries()
{
    var data = [{name: 'Brasil', id: 'BR'}];
    fillCountriesSelect(data);
    //$.getJSON("https://api.mercadolibre.com/countries", function(data)
    //{
    //    fillCountriesSelect(data);
    //});
}

function selectCountry(p_Country)
{
    var v_UserCountry = document.getElementById('user_country');
    v_UserCountry.value = p_Country;
    if(p_Country === 'BR')
        getStates();
}

function fillCountriesSelect(p_Countries)
{
    var v_CountriesSelect = document.getElementById('country');
    v_CountriesSelect.innerHTML = '';
    p_Countries.forEach(function(p_Country)
    {
        var v_Option = document.createElement('OPTION');
        v_Option.text = p_Country.name;
        v_Option.value = p_Country.id;
        v_CountriesSelect.appendChild(v_Option);
    });
    var v_UserCountry = document.getElementById('user_country').value;
    if(v_UserCountry == null || v_UserCountry == "")
    {
        selectCountry('BR');
        v_CountriesSelect.value = 'BR';
    }
    else
    {
        selectCountry(v_UserCountry);
        v_CountriesSelect.value = v_UserCountry;
    }
}

function getStates()
{
    $.getJSON("https://api.mercadolibre.com/countries/BR", function(data)
    {
        fillStatesSelect(data.states);
    });
}

function selectState(p_State)
{
    var v_UserState = document.getElementById('user_state');
    v_UserState.value = p_State;
    getCities(p_State);
}

function fillStatesSelect(p_States)
{
    var v_StatesSelect = document.getElementById('state');
    v_StatesSelect.innerHTML = '';
    p_States.forEach(function(p_State)
    {
        var v_Option = document.createElement('OPTION');
        v_Option.text = p_State.name;
        v_Option.value = p_State.id;
        v_StatesSelect.appendChild(v_Option);
    });
    var v_UserState = document.getElementById('user_state').value;
    if(v_UserState !== null || v_UserState == "")
        v_StatesSelect.value = v_UserState;
    getCities(v_UserState);
}

function getCities(p_State)
{
    $.getJSON("https://api.mercadolibre.com/states/" + p_State, function(data)
    {
        fillCitiesSelect(data.cities);
    });
}

function selectCity(p_City)
{
    var v_UserCity = document.getElementById('user_city');
    v_UserCity.value = p_City;
}

function fillCitiesSelect(p_Cities)
{
    var v_CitiesSelect = document.getElementById('city');
    v_CitiesSelect.innerHTML = '';
    p_Cities.forEach(function(p_City)
    {
        var v_Option = document.createElement('OPTION');
        v_Option.text = p_City.name;
        v_Option.value = p_City.name;
        v_CitiesSelect.appendChild(v_Option);
    });
    var v_UserCity = document.getElementById('user_city').value;
    if(v_UserCity !== null || v_UserCity == "")
        v_CitiesSelect.value = v_UserCity;
}

function manageCEP(changeCEP)
{
    if(changeCEP) {
        $('.state').replaceWith('<input class="form-control state" placeholder="Estado" type="text" name="state">');
        $('.city').replaceWith('<input class="form-control city" placeholder="Cidade" type="text" name="city">');
        $('.search-cep').addClass('disabled');

    }
    else {
        $('.state').replaceWith('<select class="state form-control" name="state">');
        $('.city').replaceWith('<select class="city form-control" name="city">');
        $('.search-cep').removeClass('disabled');
        getCountries();
    }
}

$('.search-cep').on('click', function (event)
{
    $(".cep").mask('99999999');
    var v_Cep = $(".cep").get(0).value;
    $(".cep").mask('99.999-999');

    event.preventDefault();
    $.getJSON("http://api.postmon.com.br/v1/cep/" + v_Cep).
        success(onAjaxSuccess).
        error(onAjaxError);
});

function onAjaxSuccess(data)
{
    var v_StatesSelect = document.getElementById('state');
    v_StatesSelect.value = 'BR-' + data.estado;
    selectState('BR-' + data.estado);
    selectCity(data.cidade);
    $('.street').val(data.logradouro);
    $('.neighborhood').val(data.bairro);
    $(".street").change();
}

function onAjaxError(data) {
    alert('Não foi possível encontrar um endereço com o CEP informado.');
}

$(document).ready(function()
{
    getCountries();
/*
    if($('.country').length > 0)
    {
        $.getJSON("http://restcountries.eu/rest/v1/").
            success(onAjaxCountrySuccess);
    }

    function onAjaxCountrySuccess(data)
    {
        m_LoadedCountries = true;

        if(data.length > 1)
        {
            $(data).each(function()
            {
                $('.country').append($("<option>").attr('value', this.alpha2Code).text(this.name));
            });
        }
        var countryHidden = $("input[name='select_country']");
        if(countryHidden.length > 0)
        {
            if(countryHidden.val() === '')
                selectCountry('BR');
            else
                selectCountry(countryHidden.val());
        }
        //updateStateAndCity();
        var stateHidden = $("input[name='select_state']");
        if(stateHidden.length > 0)
            selectState(stateHidden.val());
        var cityHidden = $("input[name='select_city']");
        if(cityHidden.length > 0)
            selectCity(cityHidden.val());
    }

    function updateStateAndCity()
    {
        if($('.state').is('select') && $('.city').is('select'))
        {
            new dgCidadesEstados(
            {
                estado: $('.state').get(0),
                cidade: $('.city').get(0)
            });
        }
    }

    function selectState(state)
    {
        var input = $('.state:input').not("select");
        if(input.length > 0) {
            input.val(state);
        }
        else
        {
            $('.state option[value=' + state + ']').attr('selected', 'selected').trigger('change');
        }
    }

    function selectCity(city)
    {
        var input = $('.city:input').not("select");
        if(input.length > 0)
        {
            input.val(city);
        }
        else
        {
            $(".city option").filter(function()
            {
                return this.text == city;
            }).attr('selected', 'selected').trigger('change');
        }
    }

    $(document).on('change', '.state', function(event)
    {
        event.preventDefault();
        $('.select_state').val(this.value);
    });

    $(document).on('change', '.city', function(event)
    {
        event.preventDefault();
        $('.select_city').val(this.value);
    });

    $('.country').on('change', function (event)
    {
        event.preventDefault();
        var selectedValue = $(this).find('option:selected').val();
        manageCEP(selectedValue !== '' && selectedValue !== 'BR');
    });
*/
});
</script>