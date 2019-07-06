'use strict';

var dbm;
var type;
var seed;

/**
  * We receive the dbmigrate dependency from dbmigrate initially.
  * This enables us to not have to rely on NODE_PATH.
  */
exports.setup = function(options, seedLink) {
  dbm = options.dbmigrate;
  type = dbm.dataType;
  seed = seedLink;
};

exports.up = function(db, callback) {
    db.addColumn('phppos_contract_info_add', 'birthday', {type: 'date'}, callback);
    db.addColumn('phppos_contract_info_add', 'sex', {type: 'int'}, callback);
};

exports.down = function(db, callback) {
    db.removeColumn('phppos_contract_info_add', 'birthday', callback);
    db.removeColumn('phppos_contract_info_add', 'sex', callback);
};

exports._meta = {
  "version": 1
};
