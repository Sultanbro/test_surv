<template>
	<div
		class="JobtronSelect"
		:class="{
			'JobtronSelect_compact': compact,
			'JobtronSelect_small': small,
		}"
	>
		<select
			:value="value"
			class="JobtronSelect-select"
			@change="$emit('input', $event.target.value)"
		>
			<option
				v-for="opt in options"
				:key="opt.value"
				:value="opt.value"
			>
				{{ opt.title }}
			</option>
		</select>
	</div>
</template>

<script>
// На данный момент работает только с объектами {value, title}
export default {
	name: 'JobtronSelect',
	components: {},
	props: {
		value: {
			required: true,
			validator: v => typeof v !== 'undefined'
		},
		options: {
			type: Array,
			default: () => []
		},
		compact: {
			type: Boolean,
			default: false
		},
		small: {
			type: Boolean,
		},
	}
}
</script>

<style lang="scss">
.JobtronSelect{
	position: relative;
	&-select{
		display: block;
    width: 100%;
    padding: 15px 30px 15px 20px;
    border: 0;
    outline: 0;

    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;


    font-family: "Inter", sans-serif;
    font-style: normal;
    font-weight: 400;
    font-size: 15px;
    line-height: 20px;

    color: #8DA0C1;
    background-color: #F7FAFC;
    border-radius: 6px;
		cursor: pointer;
	}
	&-clear{
		width: 25px;
    height: 25px;

    position: absolute;
    right: 18px;
    top: 14px;

		background: transparent;
    cursor: pointer;
	}
	&_selected{
		.JobtronSelect{
			&:after{
				content: url("/icon/news/birthday/close.svg");
			}
		}
	}
	&:after{
		content: url("/icon/news/inputs/select-arrow.svg");
    display: block;
    margin-top: -10px;

    pointer-events: none;

    position: absolute;
    z-index: 1;
    top: 50%;
    right: 1.5rem;
	}

	&_compact{
		.JobtronSelect{
			&-select{
				padding: 5px 15px 5px 10px;
				line-height: 18px;
			}
		}
		&:after{
			right: 0.5rem;
		}
	}
	&_small{
		.JobtronSelect{
			&-select{
				padding: 8px 26px 8px 16px;
				line-height: 20px;
			}
		}
		&:after{
			right: 0.5rem;
		}
	}
}
</style>
