<template>
  <div class="arc-form">
    <!-- <pre>{{ form }}</pre> -->
    <h2>{{ form.id ? 'Modifica' : 'Inserimento' }} Archivio</h2>
    <hr>
    <form @submit.prevent="save">
      <div class="row">
        <div class="col-sm-6">
          Data documento
          <b-form-datepicker v-model="form.doc_date" locale="it"/>
        </div>
        <div class="col-sm-6">
          Data registrazione
          <b-form-datepicker v-model="form.reg_date" locale="it"/>
        </div>
        <div class="col-sm-4">
          Protocollo esterno
          <b-form-input v-model="form.ext_pr" required/>
        </div>
        <div class="col-sm-5">
          Cod. {{ $t(`${form.type}_target`)}}
          <v-select v-model="form.sender_code" taggable :options="sender_codes"/>
        </div>
        <div class="col-sm-5">
          Nome {{ $t(`${form.type}_target`)}}
          <v-select v-model="form.sender_name" taggable :options="sender_names"/>
        </div>
        <div class="col-sm-4">
          Ufficio responsabile
          <v-select v-model="form.office" taggable :options="offices"/>
        </div>
        <div class="col-sm-4">
          Mezzo di arrivo
          <v-select v-model="form.mean_of_arrival" taggable :options="means_of_arrival"/>
        </div>
        <div class="col-sm-4">
          Locazione
          <v-select v-model="form.location" taggable :options="locations"/>
        </div>
        <div class="col-sm-3">
          Titolo
          <v-select v-model="titolo" taggable :options="_.keys(titoli)"/>
        </div>
        <div class="col-sm-3">
          Classe
          <v-select v-model="classe" taggable :options="_.keys(classi)" :disabled="!titolo"/>
        </div>
        <div class="col-sm-3">
          Sottoclasse
          <v-select v-model="sottoclasse" taggable :options="_.keys(sottoclassi)" :disabled="!classe"/>
        </div>
        <div class="col-sm-3">
          Fascicolo
          <v-select v-model="fascicolo" taggable :options="_.keys(fascicoli)" :disabled="!sottoclasse"/>
        </div>
      </div>
      <hr>
      <div class="text-right">
        <b-button variant="outline-danger" @click="$emit('close')">Annulla</b-button>
        &nbsp;
        <b-button variant="outline-success" type="submit">Salva</b-button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  props: ['form'],

  data () {
    return {
      sender_codes: [],
      sender_names: [],
      offices: [],
      means_of_arrival: [],
      locations: [],
      dossiers: {},
    }
  },

  created () {
    this.refresh()
  },

  computed: {
    // Titoli; Classe; Sottoclasse; Fascicolo
    titoli () {
      return this.dossiers || {}
    },
    classi () {
      return this.titoli[this.titolo] || {}
    },
    sottoclassi () {
      return this.classi[this.classe] || {}
    },
    fascicoli () {
      return this.sottoclassi[this.sottoclasse] || {}
    },

    dobj () {
      return (this.form.dossier || '').split(' | ')
    },

    titolo: {
      get () { return this.dobj[0] || '' },
      set (v) {
        this.$set(this.form, 'dossier', v)
      }
    },
    classe: {
      get () { return this.dobj[1] || '' },
      set (v) {
        this.form.dossier = [this.titolo, v].join(' | ')
      }
    },
    sottoclasse: {
      get () { return this.dobj[2] || '' },
      set (v) {
        this.form.dossier = [this.titolo, this.classe, v].join(' | ')
      }
    },
    fascicolo: {
      get () { return this.dobj[3] || '' },
      set (v) {
        this.form.dossier = [this.titolo, this.classe, this.sottoclasse, v].join(' | ')
      }
    },
  },

  methods: {
    refresh () {
      let params = this.filters
      axios.get(`/api/form-data`).then(res => {
        this.sender_codes = res.data.sender_codes
        this.sender_names = res.data.sender_names
        this.offices = res.data.offices
        this.means_of_arrival = res.data.means_of_arrival
        this.locations = res.data.locations
        this.dossiers = res.data.dossiers

        // this.senders = {'asd': 'prova'}
      })
    },
    save () {
      axios.post(`/api/messages/${this.form.type}`, this.form).then(res => {
        this.$emit('close')
        this.$emit('saved')
      })
    },
  },

  watch: {
    // 'form.sender_code' (v) {
    //   if (v in this.senders) {
    //     this.$set(this.form, 'sender_name', this.senders[v])
    //   }
    // }
  }
}
</script>

<style lang="scss">
.arc-form {
  form > .row > div {
    margin-bottom: 0.7rem;
  }
}
</style>
