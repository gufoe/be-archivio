<?php

namespace App;

class Message extends Model {
  public function scopeForYear($q, $year) {
    $q->where('reg_date', 'like', substr("$year", 0, 4)."%");
  }

  public static function getSenders($code = null) {
    $q = Message::orderBy('sender_code', 'asc');
    if ($code) {
      $q->where('sender_code', $code);
      return @$q->first()->sender_name;
    }
    return (object) $q->pluck('sender_name', 'sender_code')->all();
  }
  public function setSenderCodeAttribute($code) {
    $this->attributes['sender_code'] = mb_strtoupper($code);
  }
  public static function getDossiers() {
    $x = Message::distinct()->pluck('dossier');
    $ret = (object) [];
    foreach ($x as $d) {
      $d = explode(' | ', $d);
      if (count($d) != 4) continue;
      if (!isset($ret->{$d[0]})) {
        $ret->{$d[0]} = (object) [];
      }
      if (!isset($ret->{$d[0]}->{$d[1]})) {
        $ret->{$d[0]}->{$d[1]} = (object) [];
      }
      if (!isset($ret->{$d[0]}->{$d[1]}->{$d[2]})) {
        $ret->{$d[0]}->{$d[1]}->{$d[2]} = (object) [];
      }
      if (!isset($ret->{$d[0]}->{$d[1]}->{$d[2]}->{$d[3]})) {
        $ret->{$d[0]}->{$d[1]}->{$d[2]}->{$d[3]} = (object) [];
      }
    }
    return $ret;
  }
}
