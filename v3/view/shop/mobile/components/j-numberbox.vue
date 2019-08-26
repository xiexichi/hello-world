<template>
    <view class="uni-numbox">
        <view class="uni-numbox__minus" :class="{'uni-numbox--disabled': disableSubtract||disabled}" @click="_calcValue('subtract')">-</view>
        <input class="uni-numbox__value" type="number" :disabled="disabled" :value="inputValue" @blur="_onBlur">
        <view class="uni-numbox__plus" :class="{'uni-numbox--disabled': disableAdd||disabled}" @click="_calcValue('add')">+</view>
    </view>
</template>
<script>
    export default {
        name: 'j-numberbox',
        props: {
            value: {
                type: [Number, String],
                default: 1
            },
            min: {
                type: [Number, String],
                default: 0
            },
            max: {
                type: [Number, String],
                default: 100
            },
            step: {
                type: [Number, String],
                default: 1
            },
            disabled: {
                type: Boolean,
                default: false
            },
            id: {
                type: [String, Number],
                default: ''
            }
        },
        data() {
            return {
                inputValue: this.value
            }
        },
        computed: {
            disableSubtract() {
                return this.inputValue <= this.min
            },
            disableAdd() {
                return this.inputValue >= this.max
            }
        },
        watch: {
            value(val) {
                this.inputValue = val;
            }
        },
        methods: {
            _calcValue(type) {
                if (this.disabled) {
                    return
                }
                const scale = this._getDecimalScale()
                let value = this.inputValue * scale
                let step = this.step * scale
                if (type === 'subtract') {
                    value -= step
                } else if (type === 'add') {
                    value += step
                }
                if (value < this.min || value > this.max) {
                    return
                }
                this.inputValue = value / scale;
				const event = {
					id: this.id,
					detail: {
						value: this.inputValue
					}
				}
				this.onChange(event)
            },
            _getDecimalScale() {
                let scale = 1
                // 浮点型
                if (~~this.step !== this.step) {
                    scale = Math.pow(10, (this.step + '').split('.')[1].length)
                }
                return scale
            },
            _onBlur(event) {
                let value = event.detail.value
                if (!value) {
                    this.inputValue = 0
                    return
                }
                value = +value;
                if (value > this.max) {
                    return this.max
                } else if (value < this.min) {
                    return this.min
                }
                this.inputValue = value
				this.onChange(event)
            },
			onChange(e) {
				e.id = this.id
			    this.$emit('change', e)
			}
        }
    }
</script>
<style lang="scss">
    $numbox-btn-width:56upx;
    $numbox-input-width:68upx;
    $numbox-height:56upx;
    $uni-font-size-xxl:38upx;

    .uni-numbox {
        display: inline-flex;
        flex-direction: row;
        justify-content: flex-start;
        height: $numbox-height;
        position: relative;

        &:after {
            content: '';
            position: absolute;
            transform-origin: center;
            box-sizing: border-box;
            pointer-events: none;
            top: -50%;
            left: -50%;
            right: -50%;
            bottom: -50%;
            border: 1px solid $uni-border-color;
            border-radius: $uni-border-radius-lg;
            transform: scale(.5);
        }

        &__minus,
        &__plus {
            margin: 0;
            background-color: $uni-bg-color-grey;
            width: $numbox-btn-width;
            font-size: $uni-font-size-xxl;
            height: 100%;
            line-height: $numbox-height;
            text-align: center;
            color: $uni-text-color;
            position: relative;
        }

        &__value {
            position: relative;
            background-color: $uni-bg-color;
            width: $numbox-input-width;
            height: 100%;
            text-align: center;
            padding: 0;

            &:after {
                content: '';
                position: absolute;
                transform-origin: center;
                box-sizing: border-box;
                pointer-events: none;
                top: -50%;
                left: -50%;
                right: -50%;
                bottom: -50%;
                border-style: solid;
                border-color: $uni-border-color;
                border-left-width: 1px;
                border-right-width: 1px;
                border-top-width: 0;
                border-bottom-width: 0;
                transform: scale(.5);
            }
        }

        &--disabled {
            color: $uni-text-color-disable;
        }
    }
</style>
