import {checkIfModelHasEmptyProperties, toObjectRequest} from "@/HelperFunctions";

export default class Form {

    toObjectRequest() {
        return toObjectRequest(this);
    }

    hasEmptyProperties() {
        return checkIfModelHasEmptyProperties(this);
    }

    static copy(form) {
        return new Form(form.id, form.name, form.description, form.active);
    }

    static createFormsFromArray(models) {
        let forms = []
        models.forEach(function (model) {
            forms.push(Form.fromModel(model));
        })
        return forms;
    }

    static fromModel(model) {
        return new Form(model.id, model.name, model.description, model.active);
    }

    constructor(id = null, name = '', description = '', active = false) {
        this.id = id;
        this.name = name;
        this.description = description;
        this.active = active;

        this.dataStructure = {
            id: null,
            name: 'required',
            description: null,
            active:0,

        }
    }
}
