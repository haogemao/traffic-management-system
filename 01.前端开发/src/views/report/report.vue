<template>
  <Card :bordered="false" :dis-hover="true">
    <Row>
      <Col span="20">
        <Button type="primary" @click="AddReportStatus()">Add report</Button>
      </Col>
      <Col span="4">
        <Input
          search
          enter-button="Search"
          placeholder="Enter keywords..."
          @on-search="GetReportList"
          v-model="report_search_keywords"
        />
      </Col>
    </Row>
    <div class="page-table">
      <Table
        :columns="report_column"
        :data="report_data"
        no-data-text="NO DATA"
      >
        <template slot-scope="{ row }" slot="id">
          <strong>{{ row.id + 1 }}</strong>
        </template>
        <template slot-scope="{ index }" slot="PersonInvolved">
          <Button type="text" size="small" @click="PartPersonStatus(index)"
            >View</Button
          >
        </template>
        <template slot-scope="{ index }" slot="VehicleInvolved">
          <Button type="text" size="small" @click="PartVehicleStatus(index)"
            >View</Button
          >
        </template>
        <template slot-scope="{ index }" slot="OffenceInvolved">
          <Button type="text" size="small" @click="PartOffenceStatus(index)"
            >View</Button
          >
        </template>
        <template slot-scope="{ index }" slot="action">
          <Button type="success" size="small" @click="EditReportStatus(index)"
            >Edit</Button
          >
          <Button type="error" size="small" @click="DeleteReport(index)"
            >Delete</Button
          >
        </template>
      </Table>
      <Card :bordered="false" :dis-hover="true">
        <Page
          v-if="content_total != 0"
          :total="content_total"
          :page-size="page_size"
          @on-change="pageChange"
        />
      </Card>
    </div>
    <Modal
      v-model="add_report_status"
      title="Add report"
      @on-ok="AddReport()"
      width="650"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Form ref="formReport1" :model="formReport1" :label-width="110">
        <FormItem label="Handler">
          <Input v-model="formReport1.Handler" disabled />
        </FormItem>
        <FormItem label="PersonInvolved">
          <Tag
            v-for="item in formReport1.PersonInvolved"
            :key="item.id"
            :name="item.id"
            closable
            @on-close="PersonTagClose"
            >{{ item.Person_name }}</Tag
          >
          <Button
            icon="ios-add"
            type="dashed"
            size="small"
            @click="PersonTagAdd"
            >Add</Button
          >
        </FormItem>
        <FormItem label="VehicleInvolved">
          <Tag
            v-for="item in formReport1.VehicleInvolved"
            :key="item.id"
            :name="item.id"
            closable
            @on-close="VehicleTagClose"
            >{{ item.Vehicle_id }}</Tag
          >
          <Button
            icon="ios-add"
            type="dashed"
            size="small"
            @click="VehicleTagAdd"
            >Add</Button
          >
        </FormItem>
        <FormItem label="OffenceInvolved">
          <Tag
            v-for="item in formReport1.OffenceInvolved"
            :key="item.id"
            :name="item.id"
            closable
            @on-close="OffenceTagClose"
            >{{ item.Offence_description }}</Tag
          >
          <Button
            icon="ios-add"
            type="dashed"
            size="small"
            @click="OffenceTagAdd"
            >Add</Button
          >
        </FormItem>
        <FormItem label="Final fine">
          <InputNumber
            :max="9999"
            :min="0"
            v-model="formReport1.Finalfine"
            :disabled="!isadmin"
          ></InputNumber>
        </FormItem>
        <FormItem label="Submit time">
          <TimePicker
            type="time"
            format="yyyymmdd hhmmss"
            v-model="formReport1.Submit_time"
            placeholder="Select time"
          ></TimePicker>
        </FormItem>
        <FormItem label="Description">
          <Input
            v-model="formReport1.Report_description"
            maxlength="1000"
            :rows="8"
            show-word-limit
            type="textarea"
            placeholder="Enter Description..."
          />
        </FormItem>
      </Form>
    </Modal>
    <Modal
      v-model="view_all_person_list_status"
      title="Add"
      width="650"
      @on-ok="PersonTagAddOk()"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Table
        height="400"
        border
        :columns="person_column"
        :data="person_data"
        no-data-text="NO DATA"
        @on-selection-change="PersonSelectChange"
      >
      </Table>
    </Modal>
    <Modal
      v-model="view_all_vehicle_list_status"
      title="Add"
      width="650"
      @on-ok="VehicleTagAddOk()"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Table
        height="400"
        border
        :columns="vehicle_column"
        :data="vehicle_data"
        no-data-text="NO DATA"
        @on-selection-change="VehicleSelectChange"
      >
      </Table>
    </Modal>
    <Modal
      v-model="view_all_offence_list_status"
      title="Add"
      width="650"
      @on-ok="OffenceTagAddOk()"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Table
        height="400"
        border
        :columns="offence_column"
        :data="offence_data"
        no-data-text="NO DATA"
        @on-selection-change="OffenceSelectChange"
      >
      </Table>
    </Modal>
    <Modal
      v-model="edit_report_status"
      title="Edit the report"
      @on-ok="EditReport()"
      width="650"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Form ref="formReport" :model="formReport" :label-width="110">
        <FormItem label="Handler">
          <Input v-model="formReport.Handler" disabled />
        </FormItem>
        <FormItem label="PersonInvolved">
          <Tag
            v-for="item in formReport.PersonInvolved"
            :key="item.id"
            :name="item.id"
            closable
            @on-close="PersonTagClose"
            >{{ item.Person_name }}</Tag
          >
          <Button
            icon="ios-add"
            type="dashed"
            size="small"
            @click="PersonTagAdd"
            >Add</Button
          >
        </FormItem>
        <FormItem label="VehicleInvolved">
          <Tag
            v-for="item in formReport.VehicleInvolved"
            :key="item.id"
            :name="item.id"
            closable
            @on-close="VehicleTagClose"
            >{{ item.Vehicle_id }}</Tag
          >
          <Button
            icon="ios-add"
            type="dashed"
            size="small"
            @click="VehicleTagAdd"
            >Add</Button
          >
        </FormItem>
        <FormItem label="OffenceInvolved">
          <Tag
            v-for="item in formReport.OffenceInvolved"
            :key="item.id"
            :name="item.id"
            closable
            @on-close="OffenceTagClose"
            >{{ item.Offence_description }}</Tag
          >
          <Button
            icon="ios-add"
            type="dashed"
            size="small"
            @click="OffenceTagAdd"
            >Add</Button
          >
        </FormItem>
        <FormItem label="Final fine">
          <InputNumber
            :max="9999"
            :min="0"
            v-model="formReport.Finalfine"
            :disabled="!isadmin"
          ></InputNumber>
        </FormItem>
        <FormItem label="Submit time">
          <TimePicker
            type="time"
            format="yyyymmdd hhmmss"
            v-model="formReport.Submit_time"
            placeholder="Select time"
          ></TimePicker>
        </FormItem>
        <FormItem label="Description">
          <Input
            v-model="formReport.Report_description"
            maxlength="1000"
            :rows="8"
            show-word-limit
            type="textarea"
            placeholder="Enter Description..."
          />
        </FormItem>
      </Form>
    </Modal>
    <Modal
      v-model="view_part_person_list_status"
      title="PersonInvolved"
      width="650"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Table
        height="400"
        border
        :columns="part_person_column"
        :data="part_person_data"
        no-data-text="NO DATA"
      >
      </Table>
    </Modal>
    <Modal
      v-model="view_part_vehicle_list_status"
      title="VehicleInvolved"
      width="650"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Table
        height="400"
        border
        :columns="part_vehicle_column"
        :data="part_vehicle_data"
        no-data-text="NO DATA"
      >
      </Table>
    </Modal>
    <Modal
      v-model="view_part_offence_list_status"
      title="OffenceInvolved"
      width="650"
      ok-text="Confirm"
      cancel-text="Cancel"
    >
      <Table
        height="400"
        border
        :columns="part_offence_column"
        :data="part_offence_data"
        no-data-text="NO DATA"
      >
      </Table>
    </Modal>
  </Card>
</template>
<script>
export default {
  data() {
    return {
      /**
       * search keyword
       */
      report_search_keywords: "",
      /**
       * Whether is  administrator
       */
      isadmin: Number(window.sessionStorage.roleid) == 1,
      /**
       * Report data structure
       */
      formReport: {
        PersonInvolved: "",
        VehicleInvolved: "",
        OffenceInvolved: "",
        Report_id: "",
        Report_description: "",
        User_username: "",
        Finalfine: 0,
        Submit_time: "",
        Handler: window.sessionStorage.username,
      },
      formReport1: {
        PersonInvolved: "",
        VehicleInvolved: "",
        OffenceInvolved: "",
        Report_id: "",
        Report_description: "",
        User_username: "",
        Finalfine: 0,
        Submit_time: "",
        Handler: window.sessionStorage.username,
      },
      /**
       * Multiple select data
       */
      selected_person_list: {},
      selected_vehicle_list: {},
      selected_offence_list: {},
      /**
       * The owner list
       */
      person_column: [
        {
          type: "selection",
          // width: 60,
          align: "center",
        },
        {
          title: "Name",
          key: "Person_name",
        },
        {
          title: "Address",
          key: "Person_address",
        },
        {
          title: "Birth",
          key: "Person_birth",
        },
        {
          title: "Licence",
          key: "Person_license",
        },
        {
          title: "Points",
          key: "Person_points",
        },
      ],
      person_data: [
        // {
        //   Person_id: "",
        //   Person_name: "Tom",
        //   Person_address: "address",
        //   Person_birth: "2000-10-22",
        //   Person_license: "YUWEHUU21JI2J1U8",
        //   Person_points: "12",
        // },
      ],
      /**
       * Vehicle list
       */
      vehicle_column: [
        {
          type: "selection",
          // width: 60,
          align: "center",
        },
        {
          title: "VehicleID",
          key: "Vehicle_id",
        },
        {
          title: "Make",
          key: "Vehicle_make",
        },
        {
          title: "Model",
          key: "Vehicle_model",
        },
        {
          title: "Color",
          key: "Vehicle_color",
        },
      ],
      vehicle_data: [
        // {
        //   id: 0,
        //   Vehicle_id: 1,
        //   Vehicle_make: "",
        //   Vehicle_model: "",
        //   Vehicle_color: "",
        //   Person_id: "",
        // },
      ],
      /**
       * offence List
       */
      offence_column: [
        {
          type: "selection",
          // width: 60,
          align: "center",
        },
        {
          title: "Description",
          key: "Offence_description",
        },
        {
          title: "Fine",
          key: "Offence_fine",
        },
        {
          title: "Point",
          key: "Offence_point",
        },
      ],
      offence_data: [
        {
          id: "",
          Offence_id: "",
          Offence_description: "",
          Offence_fine: "",
          Offence_point: "",
        },
      ],
      /**
       * A single record corresponds to a list of owners
       */
      part_person_column: [
        {
          title: "Name",
          key: "Person_name",
        },
        {
          title: "Address",
          key: "Person_address",
        },
        {
          title: "Birth",
          key: "Person_birth",
        },
        {
          title: "Licence",
          key: "Person_license",
        },
        {
          title: "Points",
          key: "Person_points",
        },
      ],
      part_person_data: [
        // {
        //   Person_id: "",
        //   Person_name: "Tom",
        //   Person_address: "address",
        //   Person_birth: "2000-10-22",
        //   Person_license: "YUWEHUU21JI2J1U8",
        //   Person_points: "12",
        // },
      ],
      /**
       * A single record corresponds to a car list
       */
      part_vehicle_column: [
        {
          title: "VehicleID",
          key: "Vehicle_id",
        },
        {
          title: "Make",
          key: "Vehicle_make",
        },
        {
          title: "Model",
          key: "Vehicle_model",
        },
        {
          title: "Color",
          key: "Vehicle_color",
        },
      ],
      part_vehicle_data: [
        // {
        //   id: 0,
        //   Vehicle_id: 1,
        //   Vehicle_make: "",
        //   Vehicle_model: "",
        //   Vehicle_color: "",
        //   Person_id: "",
        // },
      ],
      /**
       * A single record corresponds to an offer list
       */
      part_offence_column: [
        {
          title: "Description",
          key: "Offence_description",
        },
        {
          title: "Fine",
          key: "Offence_fine",
        },
        {
          title: "Point",
          key: "Offence_point",
        },
      ],
      part_offence_data: [
        {
          id: "",
          Offence_id: "",
          Offence_description: "",
          Offence_fine: "",
          Offence_point: "",
        },
      ],
      /**
       * Dialog state control
       */
      add_report_status: false, //Add report dialog box
      view_all_person_list_status: false, //View all owners dialog box
      view_all_vehicle_list_status: false, //View  All vehicles dialog box
      view_all_offence_list_status: false, //View All fine entries dialog box
      edit_report_status: false, //Edit the report dialog box
      view_part_person_list_status: false, //View single record corresponds to the owner dialog box
      view_part_vehicle_list_status: false, //View single record corresponds to a vehicle dialog box
      view_part_offence_list_status: false, //View single record corresponds to a penalty entry dialog box
      /**
       * paging
       */
      current: 1, //What page is it on
      page_size: 10, //Each page has several pieces of data
      content_total: 20, //How many pieces of data are there
      /**
       * List reports
       */
      report_column: [
        {
          title: "ID",
          key: "id",
          slot: "id",
        },
        {
          title: "Description",
          key: "Report_description",
          ellipsis: true,
        },
        {
          title: "PersonInvolved",
          key: "PersonInvolved",
          slot: "PersonInvolved",
        },
        {
          title: "VehicleInvolved",
          key: "VehicleInvolved",
          slot: "VehicleInvolved",
        },
        {
          title: "OffenceInvolved",
          key: "OffenceInvolved",
          slot: "OffenceInvolved",
        },
        {
          title: "Finalfine",
          key: "Finalfine",
        },
        {
          title: "Handler",
          key: "User_username",
        },
        {
          title: "SubmitTime",
          key: "Submit_time",
        },
        {
          title: "Action",
          slot: "action",
          align: "center",
        },
      ],
      report_data: [
        {
          id: 0,
          Report_id: "",
          Report_description: "Tom",
          User_username: "address",
          Submit_time: "YUWEHUU21JI2J1U8",
        },
      ],
    };
  },
  methods: {
    /**
     * Paging changes the triggering function
     */
    pageChange(value) {
      var that = this;
      that.current = value; //private int current page
      that.GetReportList();
    },

    /**
     * AddReportStatus
     */
    AddReportStatus() {
      var that = this;
      that.add_report_status = true;
      (that.formReport1.PersonInvolved = ""),
        (that.formReport1.VehicleInvolved = ""),
        (that.formReport1.OffenceInvolved = ""),
        (that.formReport1.Report_id = ""),
        (that.formReport1.Report_description = ""),
        (that.formReport1.User_username = ""),
        (that.formReport1.Finalfine = 0),
        (that.formReport1.Submit_time = ""),
        (that.formReport1.Handler = window.sessionStorage.username);
    },

    /**
     * Set time
     */
    SetSubmitTime(value) {
      console.log(value);
    },

    /**
     * Three option View events for a single report record
     */
    PartPersonStatus(index) {
      var that = this;
      that.view_part_person_list_status = true;
      that.part_person_data = that.report_data[index]["person_list"];
    },
    PartVehicleStatus(index) {
      var that = this;
      that.view_part_vehicle_list_status = true;
      that.part_vehicle_data = that.report_data[index]["vehicle_list"];
    },
    PartOffenceStatus(index) {
      var that = this;
      that.view_part_offence_list_status = true;
      that.part_offence_data = that.report_data[index]["offence_list"];
    },

    /**
     * Select the Involved Person and Add Remove TAB
     */
    PersonTagAdd() {
      var that = this;
      that.view_all_person_list_status = true;
      that.GetAllPersonList();
    },
    PersonTagClose(event, name) {
      var that = this;
      const index = that.formReport.PersonInvolved.indexOf(name);
      that.formReport.PersonInvolved.splice(index, 1);
    },
    /**
     *Get a list of all owners
     */
    GetAllPersonList() {
      var that = this;
      var data = {};
      that.$axios
        .post("/api.php?function=GetAllPersonList", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.person_data = result.data;
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Select the person involved and multiple trigger functions
     */
    PersonSelectChange(selection) {
      var that = this;
      that.selected_person_list = selection;
    },
    /**
     *  the person involved to determine the event
     */
    PersonTagAddOk() {
      var that = this;
      that.formReport1.PersonInvolved = that.selected_person_list;
      that.formReport.PersonInvolved = that.selected_person_list;
    },

    /**
     * Select the vehicle involved and add or remove tags
     */
    VehicleTagAdd() {
      var that = this;
      that.view_all_vehicle_list_status = true;
      that.GetAllVehicleList();
    },
    VehicleTagClose(event, name) {
      var that = this;
      const index = that.formReport.VehicleInvolved.indexOf(name);
      that.formReport.VehicleInvolved.splice(index, 1);
    },
    /**
     * Gets a list of all vehicles
     */
    GetAllVehicleList() {
      var that = this;
      var data = {};
      that.$axios
        .post("/api.php?function=GetAllVehicleList", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.vehicle_data = result.data;
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Select the vehicle involved and Multiple trigger functions
     */
    VehicleSelectChange(selection) {
      var that = this;
      that.selected_vehicle_list = selection;
    },
    /**
     * The vehicle involved was selected to determine the incident
     */
    VehicleTagAddOk() {
      var that = this;
      that.formReport1.VehicleInvolved = that.selected_vehicle_list;
      that.formReport.VehicleInvolved = that.selected_vehicle_list;
    },

    /**
     * Select the penalty clause to add the delete TAB
     */
    OffenceTagAdd() {
      var that = this;
      that.view_all_offence_list_status = true;
      that.GetAllOffenceList();
    },
    OffenceTagClose(event, name) {
      var that = this;
      const index = that.formReport.OffenceInvolved.indexOf(name);
      that.formReport.OffenceInvolved.splice(index, 1);
    },
    /**
     * Get a list of penalty terms
     */
    GetAllOffenceList() {
      var that = this;
      var data = {};
      that.$axios
        .post("/api.php?function=GetAllOffenceList", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.offence_data = result.data;
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Opt-in penalty clause and Multiple trigger functions are selected
     */
    OffenceSelectChange(selection) {
      var that = this;
      that.selected_offence_list = selection;
    },
    /**
     *Select the penalty clause to determine the event
     */
    OffenceTagAddOk() {
      var that = this;
      that.formReport1.OffenceInvolved = that.selected_offence_list;
      that.formReport.OffenceInvolved = that.selected_offence_list;
    },

    /**
     * Add report
     */
    AddReport() {
      var that = this;
      var data = {
        // Police_id: "", //The handler id is obtained from the token information by the backend
        PersonList: that.formReport1.PersonInvolved,
        VehicleList: that.formReport1.VehicleInvolved,
        OffenceList: that.formReport1.OffenceInvolved,
        Report_description: that.formReport1.Report_description,
        Finalfine: that.formReport1.Finalfine,
        Submit_time: that.formReport1.Submit_time,
      };
      that.$axios
        .post("/api.php?function=AddReport", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            that.$Message.success(result.message);
            that.GetReportList();
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Get a list of reports
     */
    GetReportList() {
      var that = this;
      var data = {
        pageSize: that.page_size,
        currentPage: that.current,
        report_search_keywords: that.report_search_keywords,
      };
      that.$axios
        .post("/api.php?function=GetReportList", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            // that.$Message.success(result.message);
            that.report_data = result.data;
            that.content_total = result.total;
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Delete the report
     */
    DeleteReport(index) {
      var that = this;
      var data = {
        Report_id: that.report_data[index]["Report_id"],
      };
      that.$Modal.confirm({
        title: "Warning",
        okText: "Confirm",
        cancelText: "Cancel",
        content: "Are you sure to delete this record？",
        onOk: () => {
          that.$axios
            .post("/api.php?function=DeleteReport", data)
            .then(function (response) {
              var result = response.data;
              if (result.status == 1) {
                that.$Message.success(result.message);
                that.GetReportList();
              } else {
                that.$Message.error(result.message);
              }
            })
            .catch(function (error) {
              console.log(error);
            });
        },
        onCancel: () => {},
      });
    },
    /**
     * Edit the report
     */
    EditReport() {
      var that = this;
      var data = {
        Report_id: that.formReport.Report_id,
        Report_description: that.formReport.Report_description,
        PersonList: that.formReport.PersonInvolved,
        VehicleList: that.formReport.VehicleInvolved,
        OffenceList: that.formReport.OffenceInvolved,
        Finalfine: that.formReport.Finalfine,
        Submit_time: that.formReport.Submit_time,
      };
      that.$axios
        .post("/api.php?function=EditReport", data)
        .then(function (response) {
          var result = response.data;
          if (result.status == 1) {
            that.$Message.success(result.message);
            that.GetReportList();
          } else {
            that.$Message.error(result.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    /**
     * Edit the report dialog box
     */
    EditReportStatus(index) {
      var that = this;
      that.edit_report_status = true;

      (that.formReport.Report_id = that.report_data[index]["Report_id"]),
        (that.formReport.Report_description =
          that.report_data[index]["Report_description"]);
      that.formReport.Handler = that.report_data[index]["User_username"];
      that.formReport.PersonInvolved = that.report_data[index]["person_list"];
      that.formReport.VehicleInvolved = that.report_data[index]["vehicle_list"];
      that.formReport.OffenceInvolved = that.report_data[index]["offence_list"];
      (that.formReport.Submit_time = that.report_data[index]["Submit_time"]),
        (that.formReport.Finalfine = Number(
          that.report_data[index]["Finalfine"]
        ));
    },
  },
  created() {},
  mounted() {
    var that = this;
    that.GetReportList();
  },
};
</script>