<style>
    input[type="range"] {
  /* removing default appearance */
  -webkit-appearance: none;
  appearance: none; 
  /* creating a custom design */
  width: 100%;
  cursor: pointer;
  outline: none;
  border-radius: 15px;
  /*  overflow: hidden;  remove this line*/
  
  /* New additions */
  height: 6px;
  background: #ccc;
}

/* Thumb: webkit */
input[type="range"]::-webkit-slider-thumb {
  /* removing default appearance */
  -webkit-appearance: none;
  appearance: none; 
  /* creating a custom design */
  height: 15px;
  width: 15px;
  background-color: #f50;
  border-radius: 50%;
  border: none;

  /* box-shadow: -407px 0 0 400px #f50; emove this line */
  transition: .2s ease-in-out;
}

/* Thumb: Firefox */
input[type="range"]::-moz-range-thumb {
  height: 15px;
  width: 15px;
  background-color: #f50;
  border-radius: 50%;
  border: none;
  
  /* box-shadow: -407px 0 0 400px #f50; emove this line */
  transition: .2s ease-in-out;
}

/* Hover, active & focus Thumb: Webkit */

input[type="range"]::-webkit-slider-thumb:hover {
  box-shadow: 0 0 0 10px rgba(255,85,0, .1)
}
input[type="range"]:active::-webkit-slider-thumb {
  box-shadow: 0 0 0 13px rgba(255,85,0, .2)
}
input[type="range"]:focus::-webkit-slider-thumb {
  box-shadow: 0 0 0 13px rgba(255,85,0, .2)
}

/* Hover, active & focus Thumb: Firfox */

input[type="range"]::-moz-range-thumb:hover {
  box-shadow: 0 0 0 10px rgba(255,85,0, .1)
}
input[type="range"]:active::-moz-range-thumb {
  box-shadow: 0 0 0 13px rgba(255,85,0, .2)
}
input[type="range"]:focus::-moz-range-thumb {
  box-shadow: 0 0 0 13px rgba(255,85,0, .2)    
}

/*=============
Aesthetics 
=========================*/

body {
  font-family: system-ui;
}

h1 {
  color: #4b4949; 
  text-align: center;
}

.wrapper {
  color: #4b4949; 
  background: #f50;
  max-width: 400px;
  width: 100%;
  height: 300px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
}

.range {
  display: flex;
  align-items: center;
  gap: 1rem;
  max-width: 500px;
  margin: 0 auto;
  height: 4rem;
  width: 80%;
  background: #fff;
  padding: 0px 10px;
}

.value {
  font-size: 26px;    
  width: 30px;
  text-align: center;
}
</style>

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div 
        x-data="{ 
            state: $wire.entangle('{{ $getStatePath() }}'), 
            display: 0, // animated value
            sliderBackground: '',

            updateSliderBackground(value) { 
                const progress = (value / 100) * 100;
                this.sliderBackground = `background: linear-gradient(to right, #f50 ${progress}%, #ccc ${progress}%)`;
            },

            animateTo(newValue) {
                let start = this.display;
                let end = newValue;
                let startTime;

                const step = (timestamp) => {
                    if (!startTime) startTime = timestamp;
                    let progress = Math.min((timestamp - startTime) / 300, 1); // 300ms animation
                    this.display = Math.round(start + (end - start) * progress);
                    this.updateSliderBackground(this.display);
                    if (progress < 1) requestAnimationFrame(step);
                };

                requestAnimationFrame(step);
            }
        }"
        x-init="
            display = state;
            updateSliderBackground(display);
            $watch('state', value => animateTo(value));
        "
        class="flex items-center gap-4"
    >
        <input 
            type="range" 
            min="0" 
            max="100" 
            x-model="display" 
            :style="sliderBackground"
            disabled
        />

        <div class="flex items-center">
            <div class="value" x-text="display"></div>
            <span class="ml-4">%</span>
        </div>
    </div>
</x-dynamic-component>

