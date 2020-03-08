<template>
  <div class="arc-table">
    <be-container>
      <template slot="header">
        Messaggi {{ $t(type) }}

        <span v-show="is_loading">
          | Caricamento...
        </span>
      </template>
      <div slot="other" style="-max-height: 70vh; overflow-y: auto" v-if="data">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>Int.</th>
                <th>Est.</th>
                <th>Cod. {{ $t(`${type}_target`) }}</th>
                <th>Nome {{ $t(`${type}_target`) }}</th>
                <th>Data doc.</th>
                <th>Data ins.</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="mx in data.messages" :key="mx.id" @click="form = _.cloneDeep(mx)">
                <td>{{ mx.int_pr }}</td>
                <td>{{ mx.ext_pr }}</td>
                <td>{{ mx.sender_code}}</td>
                <td>{{ mx.sender_name}}</td>
                <td>{{ $d($utc(mx.doc_date), 'dm') }}</td>
                <td>{{ $d($utc(mx.reg_date), 'dm') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div>
        <div v-if="!data">
          <i>Caricamento...</i>
        </div>
        <div v-else>
          <div class="row mb-2">
            <div class="col-sm-3">
              &nbsp;
              <br>
              <b-button variant="outline-success" @click="newForm()">Inserisci nuovo</b-button>
            </div>
            <div class="col-sm-3">
              anno:
              <v-select v-model="filters.year" :options="data.years"/>
            </div>
            <div class="col-sm-3">
              ufficio:
              <v-select v-model="filters.office" :options="data.offices"/>
            </div>
            <div class="col-sm-3">
              mezzo:
              <v-select v-model="filters.mean_of_arrival" :options="data.means_of_arrival"/>
            </div>
            <div class="col-sm-4">
              locazione:
              <v-select v-model="filters.location" :options="data.locations"/>
            </div>
            <div class="col-sm-4">
              codice {{ $t(`${type}_target`) }}:
              <v-select v-model="filters.sender_code" :options="data.sender_codes"/>
            </div>
            <div class="col-sm-4">
              nome {{ $t(`${type}_target`) }}:
              <v-select v-model="filters.sender_name" :options="data.sender_names"/>
            </div>
          </div>

        </div>
      </div>

      <b-modal v-model="form_open" hide-footer hide-header size="lg">
        <arc-form v-if="form" :form="form" @saved="form = null; refresh()" @close="form = null"/>
      </b-modal>
    </be-container>
  </div>
</template>

<script>
export default {
  props: ['type'],

  data () {
    return {
      data: null,
      filters: {
        year: (new Date).getFullYear(),
      },
      form: null,
      preview: null,
      is_loading: false,
    }
  },

  created () {
    this.refresh()
  },

  computed: {
    form_open: {
      get () { return !!this.form },
      set (v) { if (!v) this.form = null }
    },
    preview_open: {
      get () { return !!this.preview },
      set (v) { if (!v) this.preview = null }
    },
  },

  methods: {
    refresh () {
      let params = this.filters
      this.is_loading = true
      axios.get(`/api/messages/${this.type}`, { params }).then(res => {
        this.data = res.data
      }).finally(() => {
        this.is_loading = false
      })
    },
    newForm () {
      this.form = {
        type: this.type,
        doc_date: (new Date()).toSql(),
        reg_date: (new Date()).toSql(),
      }
    },
  },

  watch: {
    filters: {
      deep: true,
      handler: _.debounce(function() { this.refresh() }, 200),
    }
  },
}
</script>

<style lang="scss">
.arc-table {
  .row > div {
    margin-bottom: 0.7rem;
  }
}
</style>
