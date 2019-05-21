<template>
    <el-dialog title="Export books"
               :visible.sync="visible"
               :before-close="close"
               width="35%"
               :close-on-click-modal="false"
               v-loading="loading">
        <el-form label-width="70px" label-position="left">
            <el-form-item label="Fields">
                <el-checkbox-group v-model="fields">
                    <el-checkbox label="title"></el-checkbox>
                    <el-checkbox label="author"></el-checkbox>
                </el-checkbox-group>
            </el-form-item>
            <el-form-item label="Format">
                <el-checkbox-group v-model="format">
                    <el-radio v-model="format" label="xml"></el-radio>
                    <el-radio v-model="format" label="csv"></el-radio>
                </el-checkbox-group>
            </el-form-item>
        </el-form>

        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cancel</el-button>
            <el-button @click="exportBooks()" type="primary">Export</el-button>
        </span>
    </el-dialog>
</template>

<script>
    import ApiBridge from './../api-bridge';

    export default {
        props: ['visible', 'filters', 'sorting'],

        data() {
            return {
                showDialog: false,
                fields: ['title', 'author'],
                format: 'xml',
                loading: false
            }
        },

        methods: {
            close() {
                this.$emit('closed');
                this.author = null;
            },

            exportBooks() {
                if (this.fields.length < 1) {
                    this.$notify.info({
                        title: 'Error',
                        message: 'You must choose at least 1 field'
                    });
                    return;
                }

                let params = {
                    fields: this.fields,
                    filter_groups: this.filters,
                    sort: [this.sorting],
                };

                ApiBridge.books.export(this.format, params).then((response) => {
                    let url = window.URL.createObjectURL(new Blob([response.data]));
                    let link = document.createElement('a');
                    link.href = url;

                    let filename = this.getFilenameFromResponse(response);

                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();

                    this.close();
                }).catch((error) => {
                    console.log(error);
                    this.$notify.error({
                        title: 'Error',
                        message: 'Something went wrong'
                    });
                });
            },

            getFilenameFromResponse(response) {
                let filename;

                try {
                    filename = response.headers['content-disposition'].split(' ')[1].split('=')[1];
                } catch (e) {
                    filename = 'undefined';
                }

                return filename;
            }
        }
    }
</script>