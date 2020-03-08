<template>
  <div class="arc-form">
    <!-- <pre>{{ form }}</pre> -->
    <h2>{{ is_preview ? 'Anteprima' : (form.id ? 'Modifica' : 'Inserimento') }} Archivio</h2>
    <hr>
    <form @submit.prevent="!is_preview && save()">
      <div class="row">
        <div class="col-sm-6">
          Data documento
          <b-form-datepicker v-model="form.doc_date" locale="it" :disabled="is_disabled"/>
        </div>
        <div class="col-sm-6">
          Data registrazione
          <b-form-datepicker v-model="form.reg_date" locale="it" :disabled="is_disabled"/>
        </div>
        <div class="col-sm-4">
          Protocollo esterno
          <b-form-input v-model="form.ext_pr" required :disabled="is_disabled"/>
        </div>
        <div class="col-sm-4">
          Cod. {{ $t(`${form.type}_target`)}}
          <v-select v-model="form.sender_code" taggable :options="sender_codes" :disabled="is_disabled"/>
        </div>
        <div class="col-sm-4">
          Nome {{ $t(`${form.type}_target`)}}
          <v-select v-model="form.sender_name" taggable :options="sender_names" :disabled="is_disabled"/>
        </div>
        <div class="col-sm-4">
          Ufficio responsabile
          <v-select v-model="form.office" taggable :options="offices" :disabled="is_disabled"/>
        </div>
        <div class="col-sm-4">
          Mezzo di arrivo
          <v-select v-model="form.mean_of_arrival" taggable :options="means_of_arrival" :disabled="is_disabled"/>
        </div>
        <div class="col-sm-4">
          Locazione
          <v-select v-model="form.location" taggable :options="locations" :disabled="is_disabled"/>
        </div>
        <div class="col-sm-3">
          Titolo
          <v-select v-model="titolo" taggable :options="_.keys(titoli)" :disabled="is_disabled"/>
        </div>
        <div class="col-sm-3">
          Classe
          <v-select v-model="classe" taggable :options="_.keys(classi)" :disabled="is_disabled || !titolo"/>
        </div>
        <div class="col-sm-3">
          Sottoclasse
          <v-select v-model="sottoclasse" taggable :options="_.keys(sottoclassi)" :disabled="is_disabled || !classe"/>
        </div>
        <div class="col-sm-3">
          Fascicolo
          <v-select v-model="fascicolo" taggable :options="_.keys(fascicoli)" :disabled="is_disabled || !sottoclasse"/>
        </div>
        <div class="col-sm-12">
          Documento
          <div v-if="is_preview">
            <a v-if="form.file_token" :href="'/storage/'+form.file_token">
              <template v-if="form.file_token.match(/\.(png|jpg|jpeg|gif)/i)">
                <img :src="'/storage/'+form.file_token" width="100%"/>
              </template>
              <template v-else>
                Scarica allegato
              </template>
            </a>
            <div v-else>
              <i>Nessun documento allegato</i>
            </div>
          </div>
          <b-form-file v-else v-model="form.doc" :placeholder="!form.file_token ? 'Seleziona un file' : 'Seleziona un nuovo file per sovrascrivere'" :disabled="is_disabled"/>
        </div>
      </div>
      <hr>
      <div class="text-right">

        <template v-if="is_preview">
          <b-button variant="danger" @click.prevent="del()">Elimina</b-button>
          &nbsp;
          &nbsp;
          <b-button variant="outline-danger" @click.prevent="$emit('close')">Chiudi</b-button>
          &nbsp;
          <b-button variant="outline-info" @click.prevent="is_preview = false">Modifica</b-button>
        </template>
        <template v-else>
          <b-button variant="outline-danger" @click.prevent="$emit('close')" :disabled="is_disabled">Annulla</b-button>
          &nbsp;
          <b-button variant="outline-success" type="submit" :disabled="is_disabled">Salva</b-button>
        </template>
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
      is_preview: !!this.form.id,
      is_saving: false,
    }
  },

  created () {
    this.refresh()
  },

  computed: {
    is_disabled () {
      return this.is_saving || this.is_preview
    },
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
      if (this.is_disabled) return
      this.is_saving = true
      axios.post(`/api/messages/${this.form.type}`, to_form_data(this.form)).then(res => {
        this.$emit('close')
        this.$emit('saved')
      }).finally(() => {
        this.is_saving = false
      })
    },
    del () {
      if (!confirm('Sicuri di voler eliminare questo elemento?')) return
      axios.delete(`/api/messages/${this.form.id}`, this.form).then(res => {
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
