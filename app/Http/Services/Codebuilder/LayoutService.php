<?php

namespace App\Http\Services\Codebuilder;

use App\Http\Services\Codebuilder\UtilTrait;
use Illuminate\Support\Facades\File;

class LayoutService
{
    use UtilTrait;
    public $seederList = [];
    public $seedersListFile = '';

    public $imports = '';
    public $components = '';
    public $validation = '';
    public $field = '';
    public $relComponents = [];
    public $output = '';
    public $validations = '';
    public $parentId = '';
    public $initList = '';
    public $fillList = '';
    public $initState = '';
    public $setState = '';
    public $destructureFields = '';
    public $submitFields = '';
    public $destructureLists = '';

    public function generateGridParentLayout($module, $tableName, $label, $attributesFile)
    {
        // Asegurarse de que el archivo se busca en la carpeta 'json'
        $resp = $this->verifyLayoutFile($attributesFile);
        if (isset($resp) and count($resp) > 0 and $resp[0] === false) {
            return [false, 'GRID: ' . $tableName, $label, $resp[1]];
        }
        $attributes = $resp[1];
        $optionName = $this->nameToCamelCase($tableName);
        $componentName = ucfirst($optionName . 'Grid');
        $moduleLabel = $this->nameToLabel($module);

        $fields = '';
        foreach ($attributes as $attribute) {
            if ($attribute['data'] == 'id' || $attribute['data'] == 'estado' || $attribute['data'] == 'status') {
                continue;
            }
            if (str_ends_with($attribute['data'], '_id')) {
                $fieldName = substr($attribute['data'], 0, strlen($attribute['data']) - 3) . '_name';
                $fields .= "\t\t{ name: '{$fieldName}', label: '{$attribute['title']}' },\n";
            } else {
                $fields .= "\t\t{ name: '{$attribute['data']}', label: '{$attribute['title']}' },\n";
            }
        }

        $content = <<<EOD
import React, { useState, useRef, useEffect } from "react";
import PropTypes from "prop-types";
import { useNavigate } from "react-router";
import { PageGeneral, DefaultActions, EstadoChip, Table } from "components";
import endPoints from "endPoints/endPoints";
import messages from "constantes/messages";
import { withApi, withNotification } from "wrappers";
import { ConfirmModal } from "components/dialogs";
import masterMessages from "constantes/masterMessages";
import Form from "./Form";
import { getStatusLabel } from "utils/formHelpers";

const {
  $module: {
    $optionName: {
      title,
      deleteTitle,
      deleteMessage,
      changeStatusTitle,
      changeStatusMessage,
    },
  },
} = masterMessages;

const permissions = {
  createPermissions: "general:$module:$tableName:create",
  updatePermissions: "general:$module:$tableName:update",
  deletePermissions: "general:$module:$tableName:delete",
  changeStatusPermissions: "general:$module:$tableName:changeStatus",
};

const $componentName = (props) => {
    const [idToEdit, setIdToEdit] = useState(null);
    const [idToDelete, setIdToDelete] = useState(null);
    const [idChangeStatus, setIdChangeStatus] = useState(null);
    const [openModal, setOpenModal] = useState(false);
    const tableRef = useRef(null);

    const onDelete = async () => {
        const url = `\${endPoints.$module.$optionName.base}/\${idToDelete}`;
        try {
        await props.doDelete({ url });
        tableRef.current.refresh();
        closeModalDelete();
        props.appWarning(messages.crud.delete);
        } catch (error) {
        props.appError(messages.crud.fail);
        } finally {
        setIdToDelete(null);
        }
    };

    const onChangeStatus = async () => {
        const url = `\${endPoints.$module.$optionName.base}/changestatus/\${idChangeStatus}`;
        try {
        await props.doPut({ url });
        props.appWarning(messages.crud.changeStatus);
        tableRef.current.refresh();
        } catch (error) {
        props.appError(messages.crud.fail);
        } finally {
        setIdChangeStatus(null);
        }
    };

    useEffect(() => {
        if (!openModal) {
        setIdToEdit(null);
        }
    }, [openModal]);

    const breadcrumbs = [{ label: "$module" }, { label: title }];

    const baseUrl = "/app/general/$module/$tableName";
    const navigate = useNavigate();

    const redirectNew = () => {
        navigate(`\${baseUrl}/new`, { replace: false });
    };

    const redirectEdit = (idModel) => {
        navigate(`\${baseUrl}/edit/\${idModel}`, { replace: true, id: idModel });
    };

    const openModalDelete = ({ id: idToDelete } = {}) =>
        setIdToDelete(idToDelete);
    const closeModalDelete = () => setIdToDelete(null);

    const actions = (row) => (
        <DefaultActions
            row={row}
            onEdit={() => redirectEdit(row.id)}
            onDelete={() => openModalDelete(row)}
            onChangeStatus={() => setIdChangeStatus(row.id)}
        />
    );

    const columns = [
$fields
        {
            label: "Estado",
            filter: false,
            component: (row) => <EstadoChip estado={getStatusLabel(row.status)} />,
        },
        {
            name: "acciones",
            width: 130,
            align: "right",
            label: "Acciones",
            filter: false,
            component: (row) => actions(row),
        },
    ];
    return (
        <PageGeneral breadcrumbs={breadcrumbs}>
            {Boolean(idToDelete) && (
                <ConfirmModal
                    open
                    title={deleteTitle}
                    message={deleteMessage}
                    onClose={closeModalDelete}
                    onAccept={onDelete}
                    createPermissions={permissions.deletePermissions}
                />
            )}
            {Boolean(idChangeStatus) && (
                <ConfirmModal
                open
                title={changeStatusTitle}
                message={changeStatusMessage}
                onClose={() => setIdChangeStatus(null)}
                onAccept={() => onChangeStatus()}
                createPermissions={permissions.changeStatusPermissions}
                />
            )}
            {openModal && (
                <Form
                id={idToEdit}
                setOpenModal={setOpenModal}
                refreshData={tableRef}
                />
            )}
            <Table
                forwardedRef={tableRef}
                onCreate={redirectNew}
                serverSideUrl={endPoints.$module.$optionName.base}
                columns={columns}
                title={title}
                createPermissions={permissions.createPermissions}
            />
        </PageGeneral>
    );
};

$componentName.propTypes = {
    appWarning: PropTypes.func.isRequired,
    genericException: PropTypes.func.isRequired,
    doDelete: PropTypes.func,
    doGet: PropTypes.func
};

export default withApi(withNotification($componentName));
EOD;

        // los formularios de Tipo Parent se guardan en la carpeta principal del modulo, pero los de tipo rel
        // se guardan en una subcarpeta con el nombre del formulario
        $fileName = "index.js";
        $submodule = $module . '/' . $tableName;
        $fileName = $this->saveLayoutFile($submodule, $fileName, $content);

        return [true, $componentName, $module, 'React Grid file generated: ' . $fileName];
    }

    /*if ($formType == 'parent') {
            $submodule = $module;
        } else {
            $submodule = $module . '/' . $tableName;
        }*/
    public function generateGridModalLayout($module, $tableName, $label, $attributesFile, $formParent = null)
    {
        // Asegurarse de que el archivo se busca en la carpeta 'json'
        $resp = $this->verifyLayoutFile($attributesFile);
        if (isset($resp) and count($resp) > 0 and $resp[0] === false) {
            return [false, 'GRID: ' . $tableName, $label, $resp[1]];
        }
        $attributes = $resp[1];
        $optionName = $this->nameToCamelCase($tableName);
        $componentName = ucfirst($optionName . 'Grid');
        $formName = ucfirst($optionName . 'Form');

        $fields = '';
        $this->parentId = '';
        foreach ($attributes as $attribute) {
            // obtenemos el id de la tabla padre
            if (isset($attribute['tableReference']) && $attribute['tableReference'] ==  $formParent) {
                $this->parentId = $attribute['data'];
                continue;
            }

            if ($attribute['data'] == 'id' || $attribute['data'] == 'estado' || $attribute['data'] == 'status') {
                continue;
            }
            if (str_ends_with($attribute['data'], '_id')) {
                $fieldName = substr($attribute['data'], 0, strlen($attribute['data']) - 3) . '_name';
                $fields .= "\t\t{ name: '{$fieldName}', label: '{$attribute['title']}' },\n";
            } else {
                $fields .= "\t\t{ name: '{$attribute['data']}', label: '{$attribute['title']}' },\n";
            }
        }


        $content = <<<EOD
import React, { useState, useRef } from "react";
import PropTypes from "prop-types";
import { withApi, withNotification } from "wrappers";
import { ConfirmModal } from "components/dialogs";
import { DefaultActions, Table, EstadoChip } from "components";
import endPoints from "endPoints/endPoints";
import messages from "constantes/messages";
import masterMessages from "constantes/masterMessages";
import $formName from './Form';
import { getStatusLabel } from "utils/formHelpers";

const permissions = {
  createPermissions: "general:$module:$tableName:create",
  updatePermissions: "general:$module:$tableName:update",
  deletePermissions: "general:$module:$tableName:delete",
  changeStatusPermissions: "general:$module:$tableName:changeStatus",
};

const $componentName = ({
  id,
  doDelete,
  appWarning,
  genericException,
  editable,
}) => {
  const [idToRemove, setIdToRemove] = useState(null);
  const [idToUpdate, setIdToUpdate] = useState(null);
  const $this->parentId = id;
  const [openModal, setOpenModal] = useState(false);
  const child = useRef(null);

  const openModalDelete = (idModel) => setIdToRemove(idModel);
  const closeModalDelete = () => setIdToRemove(null);

  const refreshTable$optionName = () =>  child.current.refresh();

  const onDelete = async () => {
    const params = {
      url: `\${endPoints.$module.$optionName.base}/\${idToRemove}`
    };
    try {
      await doDelete(params);
      refreshTable$optionName();
      closeModalDelete();
      appWarning(messages.crud.delete);
    } catch (error) {
      genericException(error);
    }
  };

  const openModalForm = (id = null) => {
    setOpenModal(true);
    setIdToUpdate(id);
  };

  const closeModalUpdate = () => {
    setOpenModal(false);
    setIdToUpdate(null);
  };

  const acciones = (row) => {
    const { id } = row;
    return (
      <DefaultActions
        onEdit={() => openModalForm(id)}
        onDelete={() => openModalDelete(id)}
        row={row}
      />
    );
  };

  const { deleteTitle, deleteMessage } =
    masterMessages.$module.$optionName;

  const columns = [
$fields
    {
      label: "Estado",
      filter: false,
      component: (row) => <EstadoChip estado={getStatusLabel(row.status)} />,
    },
    {
      name: "acciones",
      label: "Acciones",
      filter: false,
      component: (row) => acciones(row),
    }
  ];

   return (
    <>
      {Boolean(idToRemove) && (
        <ConfirmModal
          open
          title={deleteTitle}
          message={deleteMessage}
          onClose={closeModalDelete}
          onAccept={onDelete}
          createPermissions={permissions.deletePermissions}
        />
      )}
      {Boolean(openModal) && (
        <$formName
          id={idToUpdate}
          $this->parentId={{$this->parentId}}
          setOpenModal={closeModalUpdate}
          refreshTable={refreshTable$optionName}
          editable={editable}
        />
      )}
      <Table
        forwardedRef={child}
        serverSideUrl={endPoints.$module.$optionName.base}
        serverSideData={{ where: `$this->parentId=\${{$this->parentId}}` }}
        onCreate={openModalForm}
        columns={columns}
        createPermissions={permissions.createPermissions}
      />
    </>
  );
};

$componentName.propTypes = {
    appWarning: PropTypes.func.isRequired,
    genericException: PropTypes.func.isRequired,
    doDelete: PropTypes.func,
    doGet: PropTypes.func,
    editable: PropTypes.bool,
};

export default withApi(withNotification($componentName)); 
EOD;

        // los formularios de Tipo Parent se guardan en la carpeta principal del modulo, pero los de tipo rel
        // se guardan en una subcarpeta con el nombre del formulario
        $fileName = "index.js";
        $submodule = $module . '/' . $formParent . '/' . $tableName;
        $fileName = $this->saveLayoutFile($submodule, $fileName, $content);

        return [true, $componentName, $submodule, 'React Grid file generated: ' . $fileName];
    }


    public function generateFormParentLayout($module, $tableName, $label, $attributesFile)
    {
        // Asegurarse de que el archivo se busca en la carpeta 'json'
        echo 'file ' . $attributesFile;
        $resp = $this->verifyLayoutFile($attributesFile);
        if (isset($resp) and count($resp) > 0 and $resp[0] === false) {
            return [false, 'FORM: ' . $tableName, $label, $resp[1]];
        }
        $attributes = $resp[1];
        $optionName = $this->nameToCamelCase($tableName);
        $componentName = ucfirst($optionName . 'Form');

        $modalComponents = '';
        $modalStart = '<>';
        $modalEnd = '</>';

        // falto incluir relacionesDespacho,  en el destructureLists

        // para las listas, en el destructureList no les pone la palabra List y en el parametro de MAP tampoco

        //TODO hay que crear las funciones de las listas
        /* onOptionSelected={(selectedOption) =>
                                                handleOnChangeficha_control_acceso_id(selectedOption, subProps)
                                            } */

        // campos fecha los hace como TextBase
        // TODO Cambiar la homologacion de tipos de datos para que tome el json data_types

        // los campos de ENUM los pone autocomplete, ponerlos selectbase
        // los campos ENUM deben tener sus opciones fijas al crear el LIST
        // en el submit no envia los campos de terceros _id

        $this->imports = "";
        $this->components = "";
        $this->validations = "";
        $this->output = "";
        $this->initList = "";
        $this->fillList = "";
        $this->initState = "";
        $this->setState = "";
        $this->destructureFields = "";
        $this->submitFields = "";
        // los campos de listas para el destructure comienzan con el nombre de la tabla principal para llenar los campos de encabezado
        $this->destructureLists = "$optionName, ";

        // por cada campo, segun su tipo de dato debemos saber que componente utilizar, como validarlo y como renderizarlo
        $this->traverseJson($module, $attributes, 5, $tableName);

        $content = <<<EOD
import React, { useCallback, useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { Grid, $modalComponents } from '@mui/material';
import { Form, Field, Formik } from 'formik';
import * as Yup from 'yup';
import { $this->components Loader } from 'components';
import endPoints from 'endPoints/endPoints';
import messages from 'constantes/messages';
import { withApi, withNotification } from 'wrappers';
import { FormButtons } from 'components/controls';
import { useNavigate } from 'react-router';
import { Box } from '@mui/system';
import { getStatusLabel, getStatusValue } from 'utils/formHelpers';
$this->imports

const validationSchema = Yup.object({
   $this->validations 
});

const urlBase = endPoints.$module.$optionName.base;

$this->initList

const baseUrl = '/app/general/$module/$tableName';

const initState = {
    $this->initState
};

const selectMap = (data) => {
    return data.map((row) => ({ value: row.id, label: row.name }));
};

const $componentName = ({
    id,
    doGet,
    genericException,
    appSuccess,
    doPost,
    doPut,
    appInfo,
    setEditable,
    viewMode,
    refresh,
    }) => {
    const navigate = useNavigate();
    const [state, setState] = useState(initState);
    const [isLoading, setLoading] = useState(true);

    // Call to API for load form values
    const loadFields = useCallback(async () => {
    const params = {
        url: endPoints.$module.$optionName.initForm,
        data: id ? { id: id } : {}
    };
    const resp = await doGet(params);
    return resp;
    }, [doGet, id, refresh, setEditable]);

    const init = useCallback(async () => {
    try {
        const { $this->destructureLists } = await loadFields();
$this->fillList

        const {
$this->destructureFields
        } = $optionName;

        setState({
$this->setState
        });

        setLoading(false);
    } catch (error) {
        console.log('ERROR AL INICIAR'+error);
        genericException(error);
    }
    }, [genericException, loadFields]);

    useEffect(() => {
        init();
    }, [init]);

    const redirectEdit = (id) => {
        navigate(`\${baseUrl}/edit/\${id}`, { replace: false });
    };

    const mapValues = (values) => {
        const { $this->destructureFields } = values;
        return {
$this->submitFields
        };
    };

    const submit = async (valuesForm) => {
        const data = mapValues(valuesForm);
        const params = {
            url: id ? `\${urlBase}/\${id}` : urlBase,
            data: data
        };
        const method = id ? doPut : doPost;

        try {
            const resp = await method(params);
            if (id) {
                appInfo(messages.crud.update);
                redirectEdit(resp.response.data.id);
            } else {
                appSuccess(messages.crud.new);
                redirectEdit(resp.response.data.id);
            }
        } catch (error) {
            console.log('ERROR AL GUARDAR '+error);
            genericException(error);
        }
    };

    return (
    $modalStart
        {isLoading ? (
            <Box p={10}>
                <Loader />
            </Box>
        ) : (
            <Formik enableReinitialize initialValues={state} validationSchema={validationSchema} onSubmit={submit}>
                {(subProps) => (
                <Form>
                    {!id && viewMode ? (
                    <FormButtons formProps={subProps} />
                    ) : (
                    <FormButtons formProps={subProps} />
                    )}
                    <Grid container direction="row" spacing={2}>
    $this->output
                    </Grid>
                </Form>
                )}
            </Formik>
        )}
    $modalEnd
    );
};

$componentName.propTypes = {
    id: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    doPost: PropTypes.func,
    doGet: PropTypes.func,
    doPut: PropTypes.func,
    appInfo: PropTypes.func.isRequired,
    appSuccess: PropTypes.func.isRequired,
    genericException: PropTypes.func.isRequired,
    setEditable: PropTypes.func,
    refresh: PropTypes.oneOfType([PropTypes.object])
};

export default withApi(withNotification($componentName));
EOD;

        // los formularios de Tipo Parent se guardan en la carpeta principal del modulo, pero los de tipo rel
        // se guardan en una subcarpeta con el nombre del formulario
        $fileName = "Form.js";
        $this->generateNewLayout($module, $tableName, $optionName, $componentName);
        // si relComponents tiene contenido (definicion de Tabs), creamos el formulario EDIT con  esos componentes
        $this->generateEditLayout($module, $tableName, $optionName, $componentName, $this->relComponents);
        $submodule = $module . '/' . $tableName;

        $fileName = $this->saveLayoutFile($submodule, $fileName, $content);
        // Si un formulario tienen tablas Relacionadas $this->relComponents, le generamos la grid y el form de cacda una
        dump($this->relComponents);
        foreach ($this->relComponents as $component) {
            echo 'COMPONENTE ' . $component['label'] . "\n";
            echo 'PADRE ' . $tableName . "\n";
            // si el type del componente es grid, generamos una Grid Modal y un Form modal
            if ($component["type"] == 'grid') {
                echo ' GENERAR GRID Y FORM MODAL ' . $component["route"] . "\n";
                // GRID REL
                $result = $this->generateGridModalLayout($module, $component["route"], $component['label'], $component["route"] . '_grid.json', $tableName);
                if (isset($result) and count($result) > 1 and $result[0] == false) {
                    echo $result[1] . ' ' . $result[2] . ' ' . $result[3] . PHP_EOL;
                }
                // FORM MODAL
                $result = $this->generateFormModalLayout($module, $component["route"], $component['label'], $component["route"] . '_form.json', $tableName);
                if (isset($result) and count($result) > 1 and $result[0] == false) {
                    echo $result[1] . ' ' . $result[2] . ' ' . $result[3] . PHP_EOL;
                }
            } else if ($component["type"] == 'form') {
                echo ' GENERAR FORM CHILD ' . $component["route"] . "\n";
                // FORM CHILD
                $result = $this->generateFormChildLayout($module, $component["route"], $component['label'], $component["route"] . '_form.json', $tableName);
                if (isset($result) and count($result) > 1 and $result[0] == false) {
                    echo $result[1] . ' ' . $result[2] . ' ' . $result[3] . PHP_EOL;
                }
            }
        }
        return [true, $componentName, $module, 'React Form file generated: ' . $fileName];
    }

    public function generateFormChildLayout($module, $tableName, $label, $attributesFile, $formParent)
    {
        // Asegurarse de que el archivo se busca en la carpeta 'json'
        echo 'file CHILD ' . $attributesFile . "\n";
        echo 'tableName ' . $tableName . "\n";
        echo 'formParent ' . $formParent . "\n";
        $resp = $this->verifyLayoutFile($attributesFile);
        if (isset($resp) and count($resp) > 0 and $resp[0] === false) {
            return [false, 'FORM: ' . $tableName, $label, $resp[1]];
        }
        $attributes = $resp[1];
        $optionName = $this->nameToCamelCase($tableName);
        $componentName = ucfirst($optionName . 'Form');

        $modalComponents = '';
        $modalStart = '<>';
        $modalEnd = '</>';

        // falto incluir relacionesDespacho,  en el destructureLists

        // para las listas, en el destructureList no les pone la palabra List y en el parametro de MAP tampoco

        //TODO hay que crear las funciones de las listas
        /* onOptionSelected={(selectedOption) =>
                                                handleOnChangeficha_control_acceso_id(selectedOption, subProps)
                                            } */

        // campos fecha los hace como TextBase
        // TODO Cambiar la homologacion de tipos de datos para que tome el json data_types

        // los campos de ENUM los pone autocomplete, ponerlos selectbase
        // los campos ENUM deben tener sus opciones fijas al crear el LIST
        // en el submit no envia los campos de terceros _id

        $this->imports = "";
        $this->components = "";
        $this->validations = "";
        $this->output = "";
        $this->initList = "";
        $this->fillList = "";
        $this->initState = "";
        $this->setState = "";
        $this->destructureFields = "";
        $this->submitFields = "";
        // los campos de listas para el destructure comienzan con el nombre de la tabla principal para llenar los campos de encabezado
        $this->destructureLists = "$optionName, ";

        // por cada campo, segun su tipo de dato debemos saber que componente utilizar, como validarlo y como renderizarlo
        $this->traverseJson($module, $attributes, 5, $formParent);

        $content = <<<EOD
import React, { useCallback, useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { Grid, $modalComponents } from '@mui/material';
import { Form, Field, Formik } from 'formik';
import * as Yup from 'yup';
import { $this->components Loader } from 'components';
import endPoints from 'endPoints/endPoints';
import messages from 'constantes/messages';
import { withApi, withNotification } from 'wrappers';
import { FormButtons } from 'components/controls';
import { useNavigate } from 'react-router';
import { Box } from '@mui/system';
import { getStatusLabel, getStatusValue } from 'utils/formHelpers';

$this->imports

const validationSchema = Yup.object({
   $this->validations 
});

const urlBase = `\${endPoints.$module.$optionName.base}/updateorcreate`;

$this->initList

const parentUrl = '/app/general/$module/$formParent';

const initState = {
    $this->initState
};

const selectMap = (data) => {
    return data.map((row) => ({ value: row.id, label: row.name }));
};

const $componentName = ({
    id,
    doGet,
    genericException,
    appSuccess,
    doPost,
    doPut,
    appInfo,
    setEditable,
    viewMode,
    refresh,
    }) => {
    const navigate = useNavigate();
    const [state, setState] = useState(initState);
    const [isLoading, setLoading] = useState(true);

    // Call to API for load form values
    const loadFields = useCallback(async () => {
    const params = {
        url: endPoints.$module.$optionName.initForm,
        data: id ? { id: id } : {}
    };
    const resp = await doGet(params);
    return resp;
    }, [doGet, id, refresh, setEditable]);

    const init = useCallback(async () => {
    try {
        const { $this->destructureLists } = await loadFields();
$this->fillList

        const {
$this->destructureFields
        } = $optionName;

        setState({
$this->setState
        });

        setLoading(false);
    } catch (error) {
        console.log('ERROR AL INICIAR'+error);
        genericException(error);
    }
    }, [genericException, loadFields]);

    useEffect(() => {
        init();
    }, [init]);

    const redirectEdit = () => {
        navigate(`\${parentUrl}/edit/\${id}`, { replace: false });
    };

    const mapValues = (values) => {
        const { $this->destructureFields } = values;
        return {
        $this->parentId: id,
$this->submitFields
        };
    };

    const submit = async (valuesForm) => {
        const data = mapValues(valuesForm);
        const params = {
            url: id ? `\${urlBase}/\${id}` : urlBase,
            data: data
        };
        const method = id ? doPut : doPost;

        try {
            const resp = await method(params);
            if (id) {
                appInfo(messages.crud.update);
                redirectEdit();
            } else {
                appSuccess(messages.crud.new);
                redirectEdit();
            }
        } catch (error) {
            console.log('ERROR AL GUARDAR '+error);
            genericException(error);
        }
    };

    return (
    $modalStart
        {isLoading ? (
            <Box p={10}>
                <Loader />
            </Box>
        ) : (
            <Formik enableReinitialize initialValues={state} validationSchema={validationSchema} onSubmit={submit}>
                {(subProps) => (
                <Form>
                    {!id && viewMode ? (
                    <FormButtons formProps={subProps} />
                    ) : (
                    <FormButtons formProps={subProps} />
                    )}
                    <Grid container direction="row" spacing={2}>
    $this->output
                    </Grid>
                </Form>
                )}
            </Formik>
        )}
    $modalEnd
    );
};

$componentName.propTypes = {
    id: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    doPost: PropTypes.func,
    doGet: PropTypes.func,
    doPut: PropTypes.func,
    appInfo: PropTypes.func.isRequired,
    appSuccess: PropTypes.func.isRequired,
    genericException: PropTypes.func.isRequired,
    setEditable: PropTypes.func,
    refresh: PropTypes.oneOfType([PropTypes.object])
};

export default withApi(withNotification($componentName));
EOD;

        // los formularios de Tipo Child se guardan en la carpeta principal del modulo, pero los de tipo rel
        // se guardan en una subcarpeta con el nombre del formulario
        $fileName = "Form.js";
        $submodule = $module . '/' . $formParent . '/' . $tableName;
        $fileName = $this->saveLayoutFile($submodule, $fileName, $content);
        /* // Si un formulario tienen tablas Relacionadas $this->relComponents, le generamos la grid y el form de cacda una
        foreach ($this->relComponents as $component) {
            // si el type del componente es grid, generamos una Grid Modal y un Form modal
            if ($component["type"] == 'grid') {
                // GRID REL
                $result = $this->generateGridModalLayout($module, $component["route"], $component['label'], $component["route"] . '_grid.json', $tableName);
                if (isset($result) and count($result) > 1 and $result[0] == false) {
                    echo $result[1] . ' ' . $result[2] . ' ' . $result[3] . PHP_EOL;
                }
                // FORM MODAL
                $result = $this->generateFormModalLayout($module, $component["route"], $component['label'], $component["route"] . '_form.json', $tableName);
                if (isset($result) and count($result) > 1 and $result[0] == false) {
                    echo $result[1] . ' ' . $result[2] . ' ' . $result[3] . PHP_EOL;
                }
            } else if ($component["type"] == 'form') {
                // FORM CHILD
                $result = $this->generateModalParentLayout($module, $component["route"], $component['label'], $component["route"] . '_form.json', $tableName);
                if (isset($result) and count($result) > 1 and $result[0] == false) {
                    echo $result[1] . ' ' . $result[2] . ' ' . $result[3] . PHP_EOL;
                }
            }
        } */
        return [true, $componentName, $module, 'React Form file generated: ' . $fileName];
    }

    public function generateFormModalLayout($module, $tableName, $label, $attributesFile, $formParent)
    {
        // Asegurarse de que el archivo se busca en la carpeta 'json'
        $resp = $this->verifyLayoutFile($attributesFile);
        if (isset($resp) and count($resp) > 0 and $resp[0] === false) {
            return [false, 'FORM: ' . $tableName, $label, $resp[1]];
        }
        $attributes = $resp[1];
        $optionName = $this->nameToCamelCase($tableName);
        $componentName = ucfirst($optionName . 'Form');

        $modalComponents = 'Dialog, DialogTitle, DialogContent';
        $modalStart = '<Dialog fullWidth maxWidth="xl" open onClose={() => setOpenModal(false)} aria-labelledby="max-width-dialog-title">
        <DialogTitle id="max-width-dialog-title">' . $label . '</DialogTitle>
        <DialogContent>
            ';
        $modalEnd = '   </DialogContent>
    </Dialog>
            ';

        //TODO hay que crear las funciones de las listas
        /* onOptionSelected={(selectedOption) =>
            handleOnChangeficha_control_acceso_id(selectedOption, subProps)
        } */

        // TODO Cambiar la homologacion de tipos de datos para que tome el json data_types

        $this->imports = "";
        $this->components = "";
        $this->validations = "";
        $this->output = "";
        $this->initList = "";
        $this->fillList = "";
        $this->initState = "";
        $this->setState = "";
        $this->destructureFields = "";
        $this->submitFields = "";
        $this->destructureLists = "$optionName, ";

        // por cada campo, segun su tipo de dato debemos saber que componente utilizar, como validarlo y como renderizarlo
        $this->traverseJson($module, $attributes, 5, $formParent);

        $content = <<<EOD
import React, { useCallback, useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { Grid, $modalComponents } from '@mui/material';
import { Form, Field, Formik } from 'formik';
import * as Yup from 'yup';
import { $this->components Loader, ButtonSave, ButtonBase } from 'components';
import endPoints from 'endPoints/endPoints';
import messages from 'constantes/messages';
import { withApi, withNotification } from 'wrappers';
import { Box } from '@mui/system';
import { getStatusLabel, getStatusValue } from "utils/formHelpers";

$this->imports

const validationSchema = Yup.object({
$this->validations 
});

const urlBase = endPoints.$module.$optionName.base;

$this->initList

const initState = {
$this->initState
};

const selectMap = (data) => {
    return data.map((row) => ({ value: row.id, label: row.name }));
};

const $componentName = ({
    id,
    $this->parentId,
    doGet,
    genericException,
    appSuccess,
    doPost,
    doPut,
    appInfo,
    setEditable,
    viewMode,
    setOpenModal,
    refreshData,
    refreshTable
    }) => {
    const [state, setState] = useState(initState);
    const [isLoading, setLoading] = useState(true);

    // Call to API for load form values
    const loadFields = useCallback(async () => {
    const params = {
        url: endPoints.$module.$optionName.initForm,
        data: id ? { id: id } : {}
    };
    const resp = await doGet(params);
    return resp;
    }, [doGet, id, setEditable]);

    const init = useCallback(async () => {
    try {
        const { $this->destructureLists } = await loadFields();
$this->fillList

        const {
            $this->destructureFields
        } = $optionName;

        setState({
            $this->setState
        });

        setLoading(false);
    } catch (error) {
        console.log('ERROR AL INICIAR'+error);
        genericException(error);
    }
    }, [genericException, loadFields]);

    useEffect(() => {
        init();
    }, [init]);

    const mapValues = (values) => {
        const { $this->destructureFields } = values;
        return {
            $this->parentId: $this->parentId,
            $this->submitFields
        };
    };

    const submit = async (valuesForm) => {
        const method = id ? doPut : doPost;
        const data = mapValues(valuesForm);
        const params = {
            url: `\${urlBase}\${id ? `/\${id}` : ''}`,
            //url: id ? `\${urlBase}/\${id}` : urlBase,
            data: data
        };

        try {
            const resp = await method(params);
            if (id) {
                appInfo(messages.crud.update);
            } else {
                appSuccess(messages.crud.new);
            }
            refreshTable()
        } catch (error) {
            console.log('ERROR AL GUARDAR '+error);
            genericException(error);
        } finally {
            setOpenModal(false);
            refreshData.current.refresh();
        }
    };

    return (
    $modalStart
        {isLoading ? (
            <Box p={10}>
                <Loader />
            </Box>
        ) : (
            <Formik enableReinitialize initialValues={state} validationSchema={validationSchema} onSubmit={submit}>
                {(subProps) => (
                <Form>
                    <Grid container direction="row" spacing={2}>
    $this->output
                    </Grid>
                    <Grid container direction="row" style={{ paddingTop: 30 }} justify="flex-end">
                        <Grid item>
                            <ButtonSave />
                            <ButtonBase
                                onClick={() => setOpenModal(false)}
                                label="Cancelar"
                                style={{ marginLeft: 15 }}
                            />
                        </Grid>
                    </Grid>
                </Form>
                )}
            </Formik>
        )}
    $modalEnd
    );
};

$componentName.propTypes = {
    id: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    doPost: PropTypes.func,
    doGet: PropTypes.func,
    doPut: PropTypes.func,
    appInfo: PropTypes.func.isRequired,
    appSuccess: PropTypes.func.isRequired,
    genericException: PropTypes.func.isRequired,
    setEditable: PropTypes.func,
    refresh: PropTypes.oneOfType([PropTypes.object])
};

export default withApi(withNotification($componentName));
EOD;

        // los formularios de Tipo Parent se guardan en la carpeta principal del modulo, pero los de tipo rel/modal
        // se guardan en una subcarpeta con el nombre del formulario
        $fileName = "Form.js";
        $submodule = $module . '/' . $formParent . '/' . $tableName;

        $fileName = $this->saveLayoutFile($submodule, $fileName, $content);

        return [true, $componentName, $submodule, 'React Form file generated: ' . $fileName];
    }

    protected function generateNewLayout($module, $tableName, $optionName, $componentName)
    {
        $fileName = "New.js";
        $content = <<<EOD
import React, { useState, useRef } from 'react';
import { PageGeneral } from 'components';
import masterMessages from 'constantes/masterMessages';
import $componentName from './Form';

const { title, createTitle } = masterMessages.$module.$optionName;

const breadcrumbs = [{ label: 'Maestros' }, { label: title }];

function New() {
  const [editable, setEditable] = useState(false);
  const [viewMode, setViewMode] = useState(false);
  const child = useRef();

  return (
    <PageGeneral title={createTitle} breadcrumbs={breadcrumbs}>
      <$componentName
        refresh={child}
        setEditable={setEditable}
        setViewMode={setViewMode}
        viewMode={viewMode} 
      />
    </PageGeneral>
  );
}

New.propTypes = {};

export default New;
EOD;
        $submodule = $module . '/' . $tableName;

        $fileName = $this->saveLayoutFile($submodule, $fileName, $content);
        return 'React New file generated: ' . $fileName;
    }


    protected function generateEditLayout($module, $tableName, $optionName, $componentName, $relComponents)
    {
        $this->imports = "import {$componentName} from './Form';\n";
        $tabs = '';
        $card = '';
        foreach ($relComponents as $component) {
            if (isset($component["import"])) {
                // TODO $component["route"] debe tener la hija no el padre 
                // TODO import RelacionesDespachoFacturaGrid from './relaciones_despacho_factura/Index'
                $formRoute = $component["type"] == 'grid' ? 'index' : 'Form';
                echo 'COMPONENT TYPE ' . $component["type"];
                $this->imports .= "import {$component["import"]} from './{$component["route"]}/$formRoute';\n";
                $tabs .= "\t\t\t" . $component["tab"] . "\n";
            }
        }
        $tabs = substr($tabs, 0, strlen($tabs) - 1);

        if (!empty($tabs)) {
            $tabs = "
    const tabConfig = [
$tabs
    ]
    ";

            $card = "<Card sx={{ pl: 2, pr: 2 }}>
        <TabsComponent config={tabConfig} />
      </Card>";
        }
        $fileName = "Edit.js";
        $content = <<<EOD
import React, { useState, useRef, useEffect } from 'react';
import { withApi, withNotification } from 'wrappers';
import { PageGeneral } from 'components';
import masterMessages from 'constantes/masterMessages';
import { useParams } from 'react-router';
import TabsComponent from 'components/Tab';
import { Card, Grid } from '@mui/material';
$this->imports
const { title, updateTitle } = masterMessages.$module.$optionName;

const breadcrumbs = [{ label: 'Maestros' }, { label: title }];

const MIN_HEIGHT = 250;

function Edit() {
  const { id } = useParams();
  const [editable, setEditable] = useState(false);
  const [viewMode, setViewMode] = useState(false);

  const child = useRef();

  const refreshParent = () => child.current.refresh();
  $tabs

  return (
    <PageGeneral title={updateTitle} breadcrumbs={breadcrumbs} withOutCard>
      <Grid container spacing={2} sx={{ pb: 2, minHeight: MIN_HEIGHT }}>
        <Grid item xs={12} md={12}>
          <Card sx={{ p: 3, minHeight: '100%' }}>
              <$componentName id={id} refresh={child} setEditable={setEditable} setViewMode={setViewMode} viewMode={viewMode} 
              />
          </Card>
        </Grid>
      </Grid>
      $card
    </PageGeneral>
  );
}

export default withApi(withNotification(Edit));
EOD;
        $submodule = $module . '/' . $tableName;

        $fileName = $this->saveLayoutFile($submodule, $fileName, $content);
        return 'React New file generated: ' . $fileName;
    }

    protected function renderField($field, $formParent)
    {
        $fieldTypeMapping = [
            'field_fk' => 'SelectBase',
            'field_mediumtext' => 'TextBase',
            'field_text' => 'TextAreaBase',
            'field_enum' => 'SelectBase',
            'field_boolean' => 'SwitchBase',
            'field_date' => 'DatePickerBase',
            'field_datetime' => 'DatePickerBase'
        ];

        $component = isset($fieldTypeMapping[$field['field_type']]) ? $fieldTypeMapping[$field['field_type']] : 'TextBase';

        $fieldName = $field['data'];
        $fieldLabel = $field['label'];
        $isRequired = $field['required'];
        $isHidden = $field['hidden'];

        $parentId = "";
        $initList = "";
        $fillList = "";
        $initState = "";
        $setState = "";
        $renderedField = "";
        $destructureFields = "";
        $submitFields = "";
        $destructureLists = "";

        // verificamos si tiene el atributo tableReference y este es igual a $formParent, lo ignoramos ya que es una clave foránea
        if (isset($field['tableReference'])) {
            echo "TABLE REFERENCE " . $field['tableReference'] . ' es igual a ' . $formParent . "\n";
        }

        if (isset($field['tableReference']) && $field['tableReference'] == $formParent) {
            $this->parentId = $fieldName;
            $validation = '';
        } else {
            switch ($component) {
                case 'TextBase':
                    $initState = "$fieldName: false,\n";
                    $setState = "$fieldName: $fieldName || \"\",\n";
                    $destructureFields .= $fieldName . ", ";
                    $submitFields .= $fieldName . ", ";
                    $renderedField .= "
                        <Grid item xs={6} md={6} xl={6}>
                            <Field label=\"$fieldLabel\" name=\"$fieldName\" component={{$component}} 
                                //onClick={(event) => handleChange{$fieldName}(event)} 
                            />
                        </Grid>
            ";
                    break;

                case 'DatePickerBase':
                    $initState = "$fieldName: false,\n";
                    $setState = "$fieldName: $fieldName || \"\",\n";
                    $destructureFields .= $fieldName . ", ";
                    $submitFields .= $fieldName . ", ";
                    $renderedField .= "
                        <Grid item xs={6} md={6} xl={6}>
                            <Field label=\"$fieldLabel\" name=\"$fieldName\" component={{$component}} 
                                //onClick={(event) => handleChange{$fieldName}(event)} 
                            />
                        </Grid>
            ";
                    break;

                    // cuando es un campo boolean, se debe verificar si se llama
                    //  estado o status, para transformarlo en un SelectBase
                case 'SwitchBase':
                    if ($fieldName == 'estado' || $fieldName == 'status') {
                        $component = 'TextBase';
                        $initState = "$fieldName: 1,\n";
                        $setState = "$fieldName: getStatusLabel($fieldName) || \"Activo\",\n";
                        $destructureFields .= $fieldName . ", ";
                        $submitFields .= $fieldName . ": getStatusValue($fieldName), ";
                        $renderedField .= "
                        <Grid item xs={12} md={6} xl={6}>
                            <Field label=\"$fieldLabel\" name=\"$fieldName\" component={TextBase} disabled={true} />
                        </Grid>
            ";
                        /* $component = 'SelectBase';
                        $listNameTarget = $fieldName . 'List';
                        $options = 'JSON.parse(\'[{ "label": "Inactivo", "value": 0 },{ "label": "Activo", "value": 1 }]\')';
                        $initList = "let $listNameTarget = $options;\n";
                        $initState = "$fieldName: 1,\n";
                        $setState = "$fieldName: $fieldName || 1,\n";
                        $destructureFields .= $fieldName . ", ";
                        $submitFields .= $fieldName . ", ";
                        $renderedField .= "
                        <Grid item xs={12} md={6} xl={6}>
                            <Field label=\"$fieldLabel\" name=\"$fieldName\" component={SelectBase}  items={{$listNameTarget}} />
                        </Grid>
            "; */
                    } else {
                        $initState = "$fieldName: false,\n";
                        $setState = "$fieldName: $fieldName || false,\n";
                        $destructureFields .= $fieldName . ", ";
                        $submitFields .= $fieldName . ", ";
                        $renderedField .= "
                        <Grid item xs={6} md={6} xl={6}>
                            <Field label=\"$fieldLabel\" name=\"$fieldName\" component={{$component}}
                                //onClick={(event) => handleChange{$fieldName}(event)} 
                            />
                        </Grid>
            ";
                    }
                    break;

                    // Los SelectBase aplicacion para 2 tipos:
                    // field_fk: que es un campo de clave foránea que apunta a una tabla referenciada para obtener sus valores
                    // field_enum: que es un campo con una lista de valores fijos
                case 'SelectBase':
                    if ($field['field_type'] == 'field_fk') {
                        // verificamos si tiene el atributo tableReference y este es igual a $formParent, lo ignoramos ya que es una clave foránea a su padre

                        if ($field['data'] == $parentId) {
                            break;
                        }
                        $listNameSource = $this->fkToCamelCase($fieldName);  // Ejemplo: tercero_despacho_id se convierte en TerceroDespacho 
                        $listNameTarget = $listNameSource . 'List';
                        $destructureLists .= $listNameSource . ", ";
                        $destructureFields .= $fieldName . ", ";
                        $submitFields .= $fieldName . ", ";
                        $initState = "$fieldName: null,\n";
                        $setState = "$fieldName: $fieldName || null,\n";
                        $initList = "let $listNameTarget = [];\n";
                        $fillList = "$listNameTarget = selectMap($listNameSource);\n";
                        $renderedField .= "
                                <Grid item xs={6} md={6} xl={6}>
                                    <Field label=\"$fieldLabel\" name=\"$fieldName\" component={{$component}} items={{$listNameTarget}}  
                                        /*onOptionSelected={(selectedOption) => handleOnChange{$fieldName}(selectedOption, subProps)} */
                                    />
                                </Grid>
                    ";
                    } else if ($field['field_type'] == 'field_enum') {
                        $listNameTarget = $fieldName . 'List';
                        $options = json_encode($field['attrChoices']);
                        $initList = "let $listNameTarget = $options;\n";
                        if ($fieldName == 'estado' || $fieldName == 'status') {
                            $initState = "$fieldName: \"Activo\",\n";
                            $setState = "$fieldName: $fieldName || \"Activo\",\n";
                        } else {
                            $initState = "$fieldName: \"\",\n";
                            $setState = "$fieldName: $fieldName || \"\",\n";
                        }
                        $destructureFields .= $fieldName . ", ";
                        $submitFields .= $fieldName . ", ";
                        $renderedField .= "
                        <Grid item xs={12} md={6} xl={6}>
                            <Field label=\"$fieldLabel\" name=\"$fieldName\" component={SelectBase}  items={{$listNameTarget}} />
                        </Grid>
            ";
                    }
                    break;

                case 'AutoCompleteBase':
                    break;
            }
            $validation = $isRequired ? "\t$fieldName: Yup.string().required('{$fieldLabel} es requerido'),\n" : '';
        }

        if ($component !== 'DatePickerBase') {
            $this->components .= (strpos($this->components, $component) === false) ? $component . ", " : "";
        } else {
            $this->imports = (strpos($this->imports, $component) === false) ? "import DatePickerBase from 'components/pickers/DatePickerBase';" : "";
        }
        $this->validations .= $validation;
        $this->output .= $renderedField;
        //$this->parentId = $parentId;
        $this->initList .= $initList;
        $this->fillList .= $fillList;
        $this->initState .= "\t" . $initState;
        $this->setState .= "\t\t\t" . $setState;
        $this->destructureFields .= $destructureFields;
        $this->submitFields .= $submitFields;
        $this->destructureLists .= $destructureLists;

        return;
    }

    protected function traverseJson($module, $json, $level = 0, $formParent = null)
    {

        echo "ENTRA A traverseJson - con FormParent " . $formParent . "\n";

        foreach ($json as $element) {
            switch ($element['type']) {
                case 'row':
                    if (isset($element['children'])) {
                        //$this->output .= str_repeat("\t", $level) . "<Grid container spacing={3}>\n";
                        $this->traverseJson($module, $element['children'], $level + 1, $formParent);
                        //$this->output .= str_repeat("\t", $level - 3) . "</Grid>\n";
                    }
                    break;
                case 'tabs':
                    if (isset($element['children'])) {
                        $this->traverseJson($module, $element['children'], $level + 1, $formParent);
                    }
                    break;

                case 'column':
                    $this->renderField($element, $formParent);
                    break;

                case 'grid':
                    // indica que tiene una tabla de detalle relacionada
                    // Si es un campo tipo rel, debemos buscar si existen los archivos json para el grid y el form de esta relacion
                    // en el campo data encontramos algo como esto
                    // "data": "rel_relaciones_despacho_factura", 
                    // le quitamos la palabra rel del inicio y le concatenamos al final Grid para procesarlos
                    $jsonFile = substr($element['data'], 4);
                    $optionName = $this->nameToCamelCase($jsonFile);
                    $componentName = ucfirst($optionName . 'Grid');

                    $this->relComponents[] = [
                        "type" => 'grid',
                        "import" => $componentName,
                        "tab" => "{'label': '{$element['label']}', component: <$componentName id={id} refreshParent={refreshParent} editable={!editable}/>},",
                        "route" => $jsonFile,
                        "module" => $module,
                        "label" => $element['label']

                    ];

                    break;

                case 'form':
                    // indica que tiene una tabla relacionada 1:1
                    // Si es un campo tipo form, debemos buscar si existen los archivos json para el form de esta relacion
                    // en el campo data encontramos algo como esto
                    // "data": "rel_employee_setup", 
                    // le quitamos la palabra rel del inicio y le concatenamos al final "Form" para procesarlo
                    $jsonFile = substr($element['data'], 4);
                    $optionName = $this->nameToCamelCase($jsonFile);
                    $componentName = ucfirst($optionName . 'Form');

                    $this->relComponents[] = [
                        "type" => 'form',
                        "import" => $componentName,
                        "tab" => "{'label': '{$element['label']}', component: <$componentName id={id} refreshParent={refreshParent} editable={!editable}/>},",
                        "route" => $jsonFile,
                        "module" => $module,
                        "label" => $element['label']

                    ];

                    break;

                default:
                    $this->output .= str_repeat("\t", $level) . "Unknown type\n";
                    break;
            }
        }
        return;
    }

    protected function verifyLayoutFile($attributesFile)
    {

        // Asegurarse de que el archivo se busca en la carpeta 'json'
        $attributesFilePath = database_path('migrations/json/' . $attributesFile);
        if (!file_exists($attributesFilePath)) {
            return [false, 'The specified attributes file does not exist in json folder.'];
        }

        // Leer el contenido del archivo
        $attributesContent = file_get_contents($attributesFilePath);

        // Convertir el contenido del archivo JSON a un array
        $attributes = json_decode($attributesContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [false, 'Invalid JSON string for attributes.'];
        }
        return [true, $attributes];
    }

    public function saveLayoutFile($module, $fileName, $content)
    {

        //$layoutPath = '/home/asierra/paco/paco_front/src/pages/' . $module;
        $layoutPath = storage_path('app/autocode/' . $module . '');
        $layoutPath = $this->createDirectoryIfNotExists($layoutPath);
        File::put($layoutPath . '/' . $fileName, $content);

        return $layoutPath . '/' . $fileName;
    }
}
