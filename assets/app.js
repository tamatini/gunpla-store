/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css'
require('@fortawesome/fontawesome-free/js/all');
require('@fortawesome/fontawesome-free/css/all.min.css');

const slider = document.querySelector('#price_slider');

if (slider) {
    const min = document.querySelector('#min');
    const max = document.querySelector('#max');
    const minValue = Math.floor(parseInt(slider.dataset.min, 10)/10)*10;
    const maxValue = Math.ceil(parseInt(slider.dataset.max, 10)/10)*10;
    const range = noUiSlider.create(slider, {
        start: [min.value || minValue, max.value || maxValue],
        connect: true,
        range: {
            'min': minValue,
            'max': maxValue
        }
    })
    range.on('slide', function (values, handle) {
        if (handle === 0) {
            min.value = values[0];
        }

        if (handle === 1) {
            max.value = values[1];
        }
    })
}
