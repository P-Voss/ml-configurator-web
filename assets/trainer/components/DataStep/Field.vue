<template>
    <div class="card" :class="classes">
        <div class="card-body">
            <h5 class="card-title">
                <div class="row">
                    <div class="col-7">
                        {{ field.name }}
                    </div>
                    <div class="col-5" v-if="field.isTarget">
                        <h4>Target</h4>
                    </div>
                </div>
            </h5>
            <div class="row">
                <div class="col-6">
                    <button v-if="!field.isIgnored" @click="this.$emit('toggle-ignore', {id: field.id, val: true})" class="btn btn-primary btn-sm">
                        Ignorieren
                    </button>
                    <button v-if="field.isIgnored" @click="this.$emit('toggle-ignore', {id: field.id, val: false})" class="btn btn-primary btn-sm">
                        Nicht ignorieren
                    </button>
                </div>
                <div class="col-6">
                    <button v-if="!field.isTarget" @click="this.$emit('toggle-target', {id: field.id, val: true})" class="btn btn-primary btn-sm">
                        Als Zielvariable setzen
                    </button>
                    <button v-if="field.isTarget" @click="this.$emit('toggle-target', {id: field.id, val: false})" class="btn btn-secondary btn-sm">
                        Als Feature verwenden
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Field',
    props: {
        field: Object
    },
    computed: {
        classes() {
            if (this.field.isTarget) {
                return {
                    'bg-success': true,
                    'text-light': true,
                    'bg-gradient': true
                }
            }
            if (this.field.isIgnored) {
                return {
                    'bg-light': true,
                    'bg-gradient': true
                }
            }
            return {
                'bg-secondary': true,
                'text-light': true,
                'bg-gradient': true
            }
        }
    }
}
</script>