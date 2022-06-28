<template>
  <section>
    <!-- The Modal -->
    <div class="modal fade" :id="callerPage + '-importing-modal'" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form 
          class="form-horizontal" 
          v-on:submit.prevent="importData" 
          autocomplete="off"
          >
          <input type="hidden" name="_token" :value="csrf">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Import {{ callerPage | capitalize }}</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body" style="overflow-x: auto;height:400px;">
            <div class="form-row">
              <div class="col-md-12 mb-3">
                <label for="validationServer01">
                  Select File 
                  (
                  <a class="text-primary" :href="'/storage/samples/' + $route.name + '.xlsx'" download>
                    Download Sample File
                  </a>
                  )
                </label>
                <div class="custom-file">
                  <input 
                    type="file" 
                    class="custom-file-input" 
                    :class="! errors.wrong_file ? 'is-valid' : 'is-invalid'"
                    @change="onFileChange" 
                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                    ref="file" 
                  />
                  <label class="custom-file-label" for="validatedCustomFile">Upload Excel File...</label>
                  <div 
                    class="invalid-feedback"
                    style="display: block;" 
                    v-show="errors.wrong_file"
                  >
                    {{ errors.wrong_file }}
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="col-md-12">
                  <xlsx-read :file="excelFileToImport">
                    <xlsx-table/>
                  </xlsx-read>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn waves-effect waves-dark btn-secondary btn-outline-secondary mr-auto" data-dismiss="modal">Close</button>
            <button type="submit" class="btn waves-effect waves-dark btn-success btn-outline-success" :disabled="! submitForm">
              Upload
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
</template>

<script>
  // import XlsxRead from "vue-xlsx/dist/components/XlsxRead"
  // import XlsxJson from "vue-xlsx/dist/components/XlsxJson"
  import { XlsxRead, XlsxTable } from "vue-xlsx/dist/vue-xlsx.es.js"

  export default {

    components: {

      XlsxRead,
      XlsxTable
    
    },

    props : {

      callerPage: {
        type: String,
        required: true
      }

    },

    filters: {

      capitalize: function (value) {

        if (!value) return ''

          const words = value.split(" ");

        for (let i = 0; i < words.length; i++) {

          if (words[i]) {

            words[i] = words[i][0].toUpperCase() + words[i].substr(1);

          }

        }

        return words.join(" ");

      }

    },

    data() {

      return {

        errors : {},

        submitForm : false, 

        excelFileToImport: null,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

      };

    },

    methods: {

      onFileChange(event) {

        this.excelFileToImport = event.target.files.length ? event.target.files[0] : null;
      
        this.validateFormInput('file');

      }, 
      
      importData() {

        this.validateFormInput('file');

        if (! this.submitForm || Object.keys(this.errors).length > 0) {
          
          this.submitForm = false;
          this.excelFileToImport = null;
          this.$refs.file.value = null;
          
          return;

        }

        let formData = new FormData();
        formData.append('excelFileToImport', this.excelFileToImport);

        this.$emit('importExcelFile', formData);

        // $('#' + this.callerPage + '-importing-modal').modal('hide');

        this.excelFileToImport = null;
        this.$refs.file.value = null;

      },

      validateFormInput (formInputName) {

        this.submitForm = false;

        switch(formInputName) {

          case 'file' :

          if (! this.excelFileToImport) {
            this.errors.wrong_file = 'File is required';
          }
          else if (this.excelFileToImport.name.split(".").pop() != "xls" && this.excelFileToImport.name.split(".").pop() != "xlsx" && this.excelFileToImport.name.split(".").pop() != "csv") {

            this.excelFileToImport = null;
            this.errors.wrong_file = 'Only excel file is required';

          }
          else{

            this.submitForm = true;
            this.$delete(this.errors, 'wrong_file');

          }

          break;

        }

      }

    }

  }
</script>