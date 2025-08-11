<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
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

    <div 
        x-data="{ 
            state: $wire.entangle('{{ $getStatePath() }}'), 
            sliderBackground: '', 

            updateSliderBackground(value) { 
                const progress = (value / 100) * 100;
                this.sliderBackground = `background: linear-gradient(to right, #f50 ${progress}%, #ccc ${progress}%)`;
            } 
        }"
        x-init="$watch('state', value => updateSliderBackground(value))"
     

>
        <!-- Interact with the `state` property in Alpine.js -->
       
        <input 
        type="range" 
        min="0" 
        max="100" 
        x-model="state" 
        :style="sliderBackground"
        disabled
        />
        <div class="flex">
            <div class="value" x-text="state"></div><span class="ml-2.5" class="">%</span>
        </div>
    </div>

    
</x-dynamic-component>
